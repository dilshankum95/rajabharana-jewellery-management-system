<?php

namespace App\Http\Controllers\Customer;

use App\Enums\DesignType;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\CatalogDesign;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['catalogDesign', 'invoice'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->phone || ! $user->address || ! $user->city) {
            return redirect()
                ->route('profile.edit')
                ->with('warning', 'Please complete your customer details (phone, address, and city) before placing an order.');
        }

        $catalogDesigns = CatalogDesign::available()->with('images')->orderBy('category')->orderBy('name')->get();
        $allCategories = config('jewellery.catalog_categories');

        $categories = collect($allCategories)
            ->only($catalogDesigns->pluck('category')->unique()->sort()->values())
            ->all();

        $preselectedCatalogId = old('catalog_design_id', $request->query('catalog'));

        $selectedCategory = $preselectedCatalogId
            ? ($catalogDesigns->firstWhere('id', (int) $preselectedCatalogId)?->category ?? 'all')
            : 'all';

        return view('customer.orders.create', [
            'catalogDesigns' => $catalogDesigns,
            'categories' => $categories,
            'preselectedCatalogId' => $preselectedCatalogId,
            'selectedCategory' => $selectedCategory,
            'designCategories' => $catalogDesigns->pluck('category', 'id'),
            'goldQualities' => config('jewellery.gold_qualities'),
            'itemTypes' => config('jewellery.item_types'),
            'designTypes' => config('jewellery.design_types'),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['reference_image']);

        $data['user_id'] = $request->user()->id;
        $data['status'] = OrderStatus::Pending;

        $designType = $data['design_type'] instanceof DesignType
            ? $data['design_type']
            : DesignType::tryFrom($data['design_type']);

        if ($designType === DesignType::Catalog && ! empty($data['catalog_design_id'])) {
            $catalogDesign = CatalogDesign::find($data['catalog_design_id']);

            if ($catalogDesign?->selling_price) {
                $data['estimated_price'] = round(
                    (float) $catalogDesign->selling_price * (int) ($data['quantity'] ?? 1),
                    2
                );
            }
        }

        if ($request->hasFile('reference_image')) {
            $data['reference_image_path'] = $request->file('reference_image')
                ->store('order-designs', 'public');
        }

        $order = Order::create($data);

        $request->user()->update([
            'phone' => $data['contact_phone'],
            'address' => $data['delivery_address'],
        ]);

        $message = 'Your order has been submitted successfully. Our team will review it shortly.';
        if ($order->estimated_price) {
            $message = 'Your order has been submitted. Total price: LKR '.number_format($order->estimated_price, 2).'. Our team will review it shortly.';
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $message);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('catalogDesign', 'invoice');

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if (! in_array($order->status, [OrderStatus::Pending, OrderStatus::Confirmed])) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        $order->update(['status' => OrderStatus::Cancelled]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Your order has been cancelled.');
    }
}
