<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\FilterCustomersRequest;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(FilterCustomersRequest $request): View
    {
        $validated = $request->validated();
        $query = User::where('role', UserRole::Customer)
            ->withCount('orders')
            ->latest();

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(User $customer): View
    {
        abort_unless($customer->role === UserRole::Customer, 404);

        $customer->loadCount('orders');
        $orders = $customer->orders()->with('catalogDesign')->latest()->paginate(10);

        return view('admin.customers.show', compact('customer', 'orders'));
    }
}
