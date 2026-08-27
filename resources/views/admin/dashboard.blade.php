<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Administration</p>
                <h1 class="jewel-page-title text-xl sm:text-2xl">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name }}</h1>
                <p class="jewel-page-subtitle">{{ now()->format('l, F j, Y') }} · Business overview</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="jewel-btn-outline text-sm px-4 py-2">
                    Pending orders
                    @if($stats['pending_orders'] > 0)
                        <span class="ml-1 inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-amber-100 px-1.5 py-0.5 text-xs font-bold text-amber-800">{{ $stats['pending_orders'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.orders.index') }}" class="jewel-btn text-sm px-4 py-2">All orders</a>
            </div>
        </div>
    </x-slot>

    {{-- Metal rates strip --}}
    @if($metalPrice)
        <div class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200/60 bg-white px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="jewel-kpi-icon bg-amber-50 text-amber-700 ring-amber-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Today's metal rates</p>
                    <p class="text-sm text-slate-600">
                        Gold <span class="font-semibold text-slate-900">LKR {{ number_format($metalPrice->gold_price_per_gram, 0) }}/g</span>
                        <span class="mx-2 text-slate-300">|</span>
                        Silver <span class="font-semibold text-slate-900">LKR {{ number_format($metalPrice->silver_price_per_gram, 0) }}/g</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.metal-prices.edit') }}" class="text-sm font-semibold text-jewel-gold-dark hover:text-jewel-gold">Update rates →</a>
        </div>
    @endif

    {{-- Primary KPIs --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="jewel-kpi-card">
            <div class="jewel-kpi-icon bg-slate-100 text-slate-600 ring-slate-200/80">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total orders</p>
                <p class="mt-1 font-display text-3xl font-semibold text-slate-900">{{ number_format($stats['total_orders']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($stats['total_customers']) }} registered customers</p>
            </div>
        </div>

        <div class="jewel-kpi-card">
            <div class="jewel-kpi-icon bg-violet-50 text-violet-600 ring-violet-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">In production</p>
                <p class="mt-1 font-display text-3xl font-semibold text-slate-900">{{ number_format($stats['in_progress']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Accepted orders in production</p>
            </div>
        </div>

        <div class="jewel-kpi-card">
            <div class="jewel-kpi-icon bg-emerald-50 text-emerald-600 ring-emerald-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ready to pickup</p>
                <p class="mt-1 font-display text-3xl font-semibold text-slate-900">{{ number_format($stats['ready_orders']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Awaiting customer pickup</p>
            </div>
        </div>

        <div class="jewel-kpi-card">
            <div class="jewel-kpi-icon bg-jewel-gold-light/40 text-jewel-gold-dark ring-jewel-gold/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Quoted value</p>
                <p class="mt-1 font-display text-2xl font-semibold text-slate-900 sm:text-3xl">LKR {{ number_format($stats['quoted_value'], 0) }}</p>
                <p class="mt-1 text-xs text-slate-500">Active order estimates</p>
            </div>
        </div>
    </div>

    {{-- Attention metrics --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="group rounded-2xl border border-slate-200/60 bg-white p-4 shadow-sm transition hover:border-amber-200 hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-600">Needs review</p>
                <span class="text-xs font-semibold text-amber-700 opacity-0 transition group-hover:opacity-100">View →</span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['pending_orders'] }}</p>
            <p class="mt-1 text-xs text-slate-400">Pending approval</p>
        </a>

        <a href="{{ route('admin.orders.index', ['due' => 1]) }}" class="group rounded-2xl border p-4 shadow-sm transition {{ $stats['overdue_deliveries'] > 0 ? 'border-amber-200 bg-amber-50/50 hover:border-amber-300 hover:shadow-md' : 'border-slate-200/60 bg-white hover:border-slate-300 hover:shadow-md' }}">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium {{ $stats['overdue_deliveries'] > 0 ? 'text-amber-800' : 'text-slate-600' }}">Overdue deliveries</p>
                @if($stats['overdue_deliveries'] > 0)
                    <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                @endif
            </div>
            <p class="mt-2 text-2xl font-semibold {{ $stats['overdue_deliveries'] > 0 ? 'text-amber-900' : 'text-slate-900' }}">{{ $stats['overdue_deliveries'] }}</p>
            <p class="mt-1 text-xs {{ $stats['overdue_deliveries'] > 0 ? 'text-amber-700/70' : 'text-slate-400' }}">Past expected date</p>
        </a>

        <a href="{{ route('admin.orders.index', ['due' => 1]) }}" class="group rounded-2xl border p-4 shadow-sm transition {{ $stats['due_soon_deliveries'] > 0 ? 'border-sky-200 bg-sky-50/50 hover:border-sky-300 hover:shadow-md' : 'border-slate-200/60 bg-white hover:border-slate-300 hover:shadow-md' }}">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium {{ $stats['due_soon_deliveries'] > 0 ? 'text-sky-800' : 'text-slate-600' }}">Due within {{ config('jewellery.delivery_reminder_days') }} days</p>
            </div>
            <p class="mt-2 text-2xl font-semibold {{ $stats['due_soon_deliveries'] > 0 ? 'text-sky-900' : 'text-slate-900' }}">{{ $stats['due_soon_deliveries'] }}</p>
            <p class="mt-1 text-xs {{ $stats['due_soon_deliveries'] > 0 ? 'text-sky-700/70' : 'text-slate-400' }}">Requires scheduling</p>
        </a>
    </div>

    {{-- Due orders --}}
    <div class="mb-8 jewel-dashboard-panel">
        <div class="jewel-dashboard-panel-header">
            <div>
                <h2 class="jewel-section-title">Delivery attention</h2>
                <p class="mt-0.5 text-sm text-slate-500">Orders approaching or past their expected delivery date</p>
            </div>
            @if($dueOrders->isNotEmpty())
                <a href="{{ route('admin.orders.index', ['due' => 1]) }}" class="text-sm font-semibold text-jewel-gold-dark hover:text-jewel-gold whitespace-nowrap">Open order list →</a>
            @endif
        </div>

        @if($dueOrders->isEmpty())
            <div class="jewel-empty py-12">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="font-medium text-slate-700">All deliveries on track</p>
                <p class="mt-1 max-w-sm text-sm text-slate-400">No overdue or upcoming delivery deadlines at the moment.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="jewel-table min-w-full">
                    <thead>
                        <tr>
                            <th>Priority</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Expected delivery</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dueOrders as $order)
                            <tr @class([
                                'border-l-[3px]',
                                'border-l-amber-500 bg-amber-50/40' => $order->isDeliveryOverdue(),
                                'border-l-sky-400 bg-sky-50/30' => $order->isDeliveryDueSoon(),
                            ])>
                                <td class="whitespace-nowrap">
                                    @if($order->isDeliveryOverdue())
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Overdue
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                            Due soon
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap font-semibold text-slate-900">{{ $order->order_number }}</td>
                                <td>
                                    <p class="font-medium text-slate-900">{{ $order->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->user->email }}</p>
                                </td>
                                <td class="whitespace-nowrap font-medium {{ $order->isDeliveryOverdue() ? 'text-amber-800' : 'text-sky-800' }}">
                                    {{ $order->expected_delivery_date->format('M d, Y') }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <x-order-status-badge :status="$order->status" />
                                </td>
                                <td class="whitespace-nowrap text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-jewel-gold-dark hover:text-jewel-gold">
                                        Manage
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Activity panels --}}
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="jewel-dashboard-panel">
            <div class="jewel-dashboard-panel-header">
                <div>
                    <h2 class="jewel-section-title">Needs review</h2>
                    <p class="text-xs text-slate-400 mt-0.5">New orders awaiting staff action</p>
                </div>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-jewel-gold-dark hover:text-jewel-gold">View all</a>
            </div>
            @if($pendingOrders->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-400">No pending orders at this time.</div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($pendingOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="jewel-dashboard-list-item group">
                            <x-user-avatar :user="$order->user" size="sm" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-slate-900">{{ $order->order_number }}</p>
                                <p class="truncate text-sm text-slate-500">{{ $order->user->name }} · {{ $order->item_type_label }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                                <p class="mt-1 text-xs font-semibold text-jewel-gold-dark opacity-0 transition group-hover:opacity-100">Review →</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="jewel-dashboard-panel">
            <div class="jewel-dashboard-panel-header">
                <div>
                    <h2 class="jewel-section-title">Recent activity</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Latest orders across all statuses</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-jewel-gold-dark hover:text-jewel-gold">View all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="jewel-dashboard-list-item group">
                        <x-user-avatar :user="$order->user" size="sm" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-slate-900">{{ $order->order_number }}</p>
                            <p class="truncate text-sm text-slate-500">{{ $order->user->name }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <x-order-status-badge :status="$order->status" />
                            <p class="mt-1 text-xs text-slate-400">{{ $order->created_at->format('M d') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
