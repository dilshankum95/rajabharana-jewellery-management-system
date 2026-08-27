<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockMovementReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustRawMaterialStockRequest;
use App\Http\Requests\Admin\FilterRawMaterialRequest;
use App\Http\Requests\Admin\StoreRawMaterialRequest;
use App\Http\Requests\Admin\UpdateRawMaterialRequest;
use App\Models\RawMaterial;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RawMaterialController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    public function index(FilterRawMaterialRequest $request): View
    {
        $validated = $request->validated();
        $query = RawMaterial::query()->latest();

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (! empty($validated['material_type'])) {
            $query->where('material_type', $validated['material_type']);
        }

        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $materials = $query->paginate(15)->withQueryString();

        return view('admin.raw-materials.index', [
            'materials' => $materials,
            'materialTypes' => config('jewellery.raw_material_types'),
            'filters' => $request->only(['search', 'material_type', 'low_stock']),
            'lowStockCount' => RawMaterial::lowStock()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.raw-materials.create', $this->formOptions());
    }

    public function store(StoreRawMaterialRequest $request): RedirectResponse
    {
        RawMaterial::create($request->validated());

        return redirect()
            ->route('admin.raw-materials.index')
            ->with('success', 'Raw material added successfully.');
    }

    public function edit(RawMaterial $rawMaterial): View
    {
        $rawMaterial->load(['stockMovements' => fn ($q) => $q->with('user')->limit(10)]);

        return view('admin.raw-materials.edit', array_merge($this->formOptions(), [
            'material' => $rawMaterial,
        ]));
    }

    public function update(UpdateRawMaterialRequest $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $rawMaterial->update($request->validated());

        return redirect()
            ->route('admin.raw-materials.edit', $rawMaterial)
            ->with('success', 'Raw material updated successfully.');
    }

    public function destroy(RawMaterial $rawMaterial): RedirectResponse
    {
        if ($rawMaterial->stockMovements()->exists()) {
            return back()->with('error', 'Cannot delete a material with stock history. Mark it inactive instead.');
        }

        $rawMaterial->delete();

        return redirect()
            ->route('admin.raw-materials.index')
            ->with('success', 'Raw material deleted.');
    }

    public function adjustStock(AdjustRawMaterialStockRequest $request, RawMaterial $rawMaterial): RedirectResponse
    {
        try {
            $reason = (float) $request->validated('quantity_delta') > 0
                ? StockMovementReason::MaterialReceived
                : StockMovementReason::WorkshopUsage;

            $this->inventoryService->adjustRawMaterialStock(
                $rawMaterial,
                (float) $request->validated('quantity_delta'),
                $reason,
                $request->user(),
                null,
                $request->validated('note')
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Stock adjusted successfully.');
    }

    private function formOptions(): array
    {
        return [
            'materialTypes' => config('jewellery.raw_material_types'),
            'stockUnits' => config('jewellery.stock_units'),
        ];
    }
}
