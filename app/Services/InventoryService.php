<?php

namespace App\Services;

use App\Enums\AvailabilityStatus;
use App\Enums\DesignType;
use App\Enums\StockMovementReason;
use App\Models\CatalogDesign;
use App\Models\Order;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    public function syncCatalogAvailability(CatalogDesign $design): void
    {
        if ((int) $design->stock_quantity <= 0) {
            $design->availability_status = AvailabilityStatus::OutOfStock;
        } elseif ($design->availability_status === AvailabilityStatus::OutOfStock) {
            $design->availability_status = AvailabilityStatus::Available;
        }

        $design->save();
    }

    public function adjustCatalogStock(
        CatalogDesign $design,
        int $delta,
        StockMovementReason $reason,
        User $user,
        ?Order $order = null,
        ?string $note = null
    ): CatalogDesign {
        return DB::transaction(function () use ($design, $delta, $reason, $user, $order, $note) {
            $design = CatalogDesign::query()->lockForUpdate()->findOrFail($design->id);
            $before = (int) $design->stock_quantity;
            $after = $before + $delta;

            if ($after < 0) {
                throw new InvalidArgumentException('Insufficient stock for this catalog item.');
            }

            $design->stock_quantity = $after;
            $this->syncCatalogAvailability($design);

            $this->recordMovement($design, $before, $delta, $after, $reason, $user, $order, $note);

            return $design->fresh();
        });
    }

    public function adjustRawMaterialStock(
        RawMaterial $material,
        float $delta,
        StockMovementReason $reason,
        User $user,
        ?Order $order = null,
        ?string $note = null
    ): RawMaterial {
        return DB::transaction(function () use ($material, $delta, $reason, $user, $order, $note) {
            $material = RawMaterial::query()->lockForUpdate()->findOrFail($material->id);
            $before = (float) $material->stock_quantity;
            $after = round($before + $delta, 3);

            if ($after < 0) {
                throw new InvalidArgumentException(
                    "Insufficient {$material->name} stock. Required ".abs($delta)." {$material->unit_label}, available {$before}."
                );
            }

            $material->stock_quantity = $after;
            $material->save();

            $this->recordMovement($material, $before, $delta, $after, $reason, $user, $order, $note);

            return $material->fresh();
        });
    }

    public function deductForAcceptedOrder(Order $order, User $user): void
    {
        if ($order->design_type !== DesignType::Catalog || ! $order->catalog_design_id) {
            return;
        }

        DB::transaction(function () use ($order, $user) {
            $order->load('catalogDesign.rawMaterials');
            $design = $order->catalogDesign;

            if (! $design) {
                return;
            }

            $orderQty = max(1, (int) $order->quantity);

            $this->assertCatalogStockAvailable($design, $orderQty);
            $this->assertRawMaterialStockAvailable($design, $orderQty);

            if (! StockMovement::orderDeductionExists($order, $design)) {
                $this->adjustCatalogStock(
                    $design,
                    -$orderQty,
                    StockMovementReason::OrderAccepted,
                    $user,
                    $order,
                    "Stock deducted for order {$order->order_number}."
                );
            }

            $this->deductLinkedMaterials($order, $user, $orderQty);
        });
    }

    public function restoreForRejectedOrder(Order $order, User $user): void
    {
        if ($order->design_type !== DesignType::Catalog || ! $order->catalog_design_id) {
            return;
        }

        DB::transaction(function () use ($order, $user) {
            $order->load('catalogDesign.rawMaterials');
            $design = $order->catalogDesign;

            if (! $design) {
                return;
            }

            $orderQty = max(1, (int) $order->quantity);

            if (StockMovement::orderDeductionExists($order, $design)) {
                $this->adjustCatalogStock(
                    $design,
                    $orderQty,
                    StockMovementReason::OrderRejected,
                    $user,
                    $order,
                    "Stock restored after order {$order->order_number} was rejected."
                );
            }

            $this->restoreLinkedMaterials($order, $user, $orderQty);
        });
    }

    /** @param  list<array{raw_material_id?: mixed, quantity_required?: mixed}>|null  $rows */
    public function syncCatalogMaterials(CatalogDesign $design, ?array $rows): void
    {
        $sync = [];

        foreach ($rows ?? [] as $row) {
            $materialId = $row['raw_material_id'] ?? null;
            $quantity = $row['quantity_required'] ?? null;

            if ($materialId && $quantity !== null && $quantity !== '') {
                $sync[(int) $materialId] = ['quantity_required' => round((float) $quantity, 3)];
            }
        }

        $design->rawMaterials()->sync($sync);
    }

    public function catalogHasStock(CatalogDesign $design, int $quantity): bool
    {
        return $design->availability_status === AvailabilityStatus::Available
            && (int) $design->stock_quantity >= $quantity;
    }

    private function assertCatalogStockAvailable(CatalogDesign $design, int $orderQty): void
    {
        if ((int) $design->stock_quantity < $orderQty) {
            throw new InvalidArgumentException(
                "Cannot accept order: only {$design->stock_quantity} unit(s) in stock for {$design->name}."
            );
        }
    }

    private function assertRawMaterialStockAvailable(CatalogDesign $design, int $orderQty): void
    {
        foreach ($design->rawMaterials as $material) {
            $required = round((float) $material->pivot->quantity_required * $orderQty, 3);

            if ((float) $material->stock_quantity < $required) {
                throw new InvalidArgumentException(
                    "Cannot accept order: insufficient {$material->name}. Need {$required} {$material->unit_label}, have {$material->stock_quantity}."
                );
            }
        }
    }

    private function deductLinkedMaterials(Order $order, User $user, int $orderQty): void
    {
        foreach ($order->catalogDesign->rawMaterials as $material) {
            if (StockMovement::orderMaterialDeductionExists($order, $material)) {
                continue;
            }

            $required = round((float) $material->pivot->quantity_required * $orderQty, 3);

            $this->adjustRawMaterialStock(
                $material,
                -$required,
                StockMovementReason::OrderAccepted,
                $user,
                $order,
                "Material used for order {$order->order_number} ({$order->catalogDesign->name})."
            );
        }
    }

    private function restoreLinkedMaterials(Order $order, User $user, int $orderQty): void
    {
        foreach ($order->catalogDesign->rawMaterials as $material) {
            if (! StockMovement::orderMaterialDeductionExists($order, $material)) {
                continue;
            }

            $required = round((float) $material->pivot->quantity_required * $orderQty, 3);

            $this->adjustRawMaterialStock(
                $material,
                $required,
                StockMovementReason::OrderRejected,
                $user,
                $order,
                "Material restored after order {$order->order_number} was rejected."
            );
        }
    }

    private function recordMovement(
        CatalogDesign|RawMaterial $stockable,
        float|int $before,
        float|int $delta,
        float|int $after,
        StockMovementReason $reason,
        User $user,
        ?Order $order,
        ?string $note
    ): void {
        StockMovement::create([
            'stockable_type' => $stockable::class,
            'stockable_id' => $stockable->id,
            'quantity_before' => $before,
            'quantity_delta' => $delta,
            'quantity_after' => $after,
            'reason' => $reason,
            'order_id' => $order?->id,
            'user_id' => $user->id,
            'note' => $note,
        ]);
    }
}
