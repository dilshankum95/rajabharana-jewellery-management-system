<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="jewel-page-title text-xl">Dashboard</h1>
            <p class="jewel-page-subtitle">Overview of orders and customers</p>
        </div>
    </x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        @foreach([
            ['label' => 'Total Orders', 'value' => $stats['total_orders'], 'color' => 'text-stone-700'],
            ['label' => 'Pending', 'value' => $stats['pending_orders'], 'color' => 'text-amber-700/80'],
            ['label' => 'In Progress', 'value' => $stats['in_progress'], 'color' => 'text-violet-700/80'],
            ['label' => 'Ready', 'value' => $stats['ready_orders'], 'color' => 'text-emerald-700/80'],
            ['label' => 'Customers', 'value' => $stats['total_customers'], 'color' => 'text-stone-700'],
            ['label' => 'Quoted Value', 'value' => 'LKR '.number_format($stats['quoted_value'], 0), 'color' => 'text-stone-600 text-lg'],
        ] as $stat)
            <div class="jewel-stat">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ $stat['label'] }}</p>
                <p class="mt-2 font-display text-2xl font-semibold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Pending orders --}}
        <div class="jewel-card overflow-hidden">
            <div class="jewel-card-header flex items-center justify-between">
                <h2 class="jewel-section-title">Needs Review</h2>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-sm text-jewel-gold-dark hover:text-jewel-gold">View all</a>
            </div>
            @if($pendingOrders->isEmpty())
                <p class="px-6 py-10 text-center text-gray-400 text-sm">No pending orders</p>
            @else
                <div class="divide-y divide-jewel-gold/10">
                    @foreach($pendingOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="block px-6 py-4 hover:bg-jewel-cream/50 transition">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium text-jewel-dark">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->user->name }} · {{ $order->item_type_label }}</p>
                                </div>
                                <p class="text-xs text-gray-400 whitespace-nowrap">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent orders --}}
        <div class="jewel-card overflow-hidden">
            <div class="jewel-card-header flex items-center justify-between">
                <h2 class="jewel-section-title">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-jewel-gold-dark hover:text-jewel-gold">View all</a>
            </div>
            <div class="divide-y divide-jewel-gold/10">
                @foreach($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="block px-6 py-4 hover:bg-jewel-cream/50 transition">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-medium text-jewel-dark">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500">{{ $order->user->name }}</p>
                            </div>
                            <x-order-status-badge :status="$order->status" />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
