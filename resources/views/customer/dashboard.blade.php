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
