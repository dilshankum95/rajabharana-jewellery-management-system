<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $orders = $user->orders()
            ->with('catalogDesign')
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', OrderStatus::Pending)->count(),
            'in_progress' => $user->orders()->whereIn('status', [
                OrderStatus::Confirmed,
                OrderStatus::InProduction,
                OrderStatus::QualityCheck,
            ])->count(),
            'ready' => $user->orders()->where('status', OrderStatus::Ready)->count(),
        ];

        return view('customer.dashboard', compact('orders', 'stats'));
    }
}
