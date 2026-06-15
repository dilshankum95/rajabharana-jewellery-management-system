<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="jewel-page-title">Welcome, {{ Auth::user()->name }}</h1>
                <p class="jewel-page-subtitle">Your jewellery orders at a glance</p>
            </div>
            <a href="{{ route('orders.create') }}" class="jewel-btn shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Place New Order
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if($metalPrice)
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="jewel-card p-5 bg-gradient-to-br from-amber-50 to-white border-amber-200/60">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-amber-100 text-amber-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wider text-amber-700/80 font-semibold">Gold · Today</p>
                                <p class="font-display text-2xl font-semibold text-amber-900 mt-0.5">
                                    LKR {{ number_format($metalPrice->gold_price_per_gram, 2) }}
                                    <span class="text-sm font-normal text-amber-700/70">/ gram</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="jewel-card p-5 bg-gradient-to-br from-slate-50 to-white border-slate-200/80">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-slate-200 text-slate-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Silver · Today</p>
                                <p class="font-display text-2xl font-semibold text-slate-800 mt-0.5">
                                    LKR {{ number_format($metalPrice->silver_price_per_gram, 2) }}
                                    <span class="text-sm font-normal text-slate-500">/ gram</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 -mt-4">
                    Rates as of {{ $metalPrice->price_date->format('M d, Y') }}. Updated by Rajabharana.
                </p>
            @endif

            @if(($overdueDeliveryCount ?? 0) > 0)
                <x-alert type="info">
                    <p class="font-semibold">{{ $overdueDeliveryCount }} order(s) still in progress past the expected delivery date</p>
                    <p class="mt-1 text-sm">Our team is working on your order and will update you if the delivery date changes.</p>
                </x-alert>
            @endif

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['label' => 'Total Orders', 'value' => $stats['total'], 'color' => 'text-stone-700'],
                    ['label' => 'Pending Review', 'value' => $stats['pending'], 'color' => 'text-amber-700/80'],
                    ['label' => 'In Progress', 'value' => $stats['in_progress'], 'color' => 'text-violet-700/80'],
                    ['label' => 'Ready', 'value' => $stats['ready'], 'color' => 'text-emerald-700/80'],
                ] as $stat)
                    <div class="jewel-stat">
                        <p class="text-xs uppercase tracking-wider text-gray-400">{{ $stat['label'] }}</p>
                        <p class="mt-2 font-display text-3xl font-semibold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="jewel-card overflow-hidden">
                <div class="jewel-card-header flex items-center justify-between">
                    <h3 class="jewel-section-title">Recent Orders</h3>
                    @if($orders->count())
                        <a href="{{ route('orders.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">View all &rarr;</a>
                    @endif
                </div>

                @if($orders->isEmpty())
                    <x-empty-state title="No orders yet" description="Start your jewellery journey by placing your first custom order.">
                        <a href="{{ route('orders.create') }}" class="jewel-btn">Place Your First Order</a>
                    </x-empty-state>
                @else
                    <div class="divide-y divide-jewel-gold/10">
                        @foreach($orders as $order)
                            <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-jewel-cream/50 transition group">
                                <div>
                                    <p class="font-semibold text-jewel-dark group-hover:text-jewel-gold-dark transition">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->item_type_label }} @if($order->item_name)— {{ $order->item_name }}@endif</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if($order->hasPrice())
                                        <span class="text-sm font-semibold text-jewel-gold-dark hidden sm:block">
                                            LKR {{ number_format($order->estimated_price, 0) }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400 hidden sm:block">{{ $order->created_at->format('M d, Y') }}</span>
                                    <x-order-status-badge :status="$order->status" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
