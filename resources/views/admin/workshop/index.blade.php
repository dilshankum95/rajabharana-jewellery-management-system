<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="jewel-page-title text-xl">Workshop</h1>
                <p class="jewel-page-subtitle">Production queue and technician assignments</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.workshop.technicians') }}"
                    class="jewel-btn-outline text-sm px-4 py-2">
                    Technicians
                </a>
                @can('permission', 'users.manage')
                    <a href="{{ route('admin.users.create') }}" class="jewel-btn text-sm px-4 py-2">+ Add Technician</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">In Queue</p>
            <p class="font-display text-3xl font-semibold text-jewel-charcoal mt-1">{{ $stats['queue_total'] }}</p>
        </div>
        <a href="{{ route('admin.workshop.index', ['unassigned' => 1]) }}"
            class="jewel-card p-5 transition hover:ring-2 hover:ring-amber-200 {{ ($filters['unassigned'] ?? false) ? 'ring-2 ring-amber-400 bg-amber-50/50' : '' }}">
            <p class="text-xs uppercase tracking-wider text-amber-700/80 font-semibold">Unassigned</p>
            <p class="font-display text-3xl font-semibold text-amber-800 mt-1">{{ $stats['unassigned'] }}</p>
        </a>
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-violet-600/80 font-semibold">In Production</p>
            <p class="font-display text-3xl font-semibold text-violet-700 mt-1">{{ $stats['in_production'] }}</p>
        </div>
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-indigo-600/80 font-semibold">Quality Check</p>
            <p class="font-display text-3xl font-semibold text-indigo-700 mt-1">{{ $stats['quality_check'] }}</p>
        </div>
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-emerald-600/80 font-semibold">Ready to Pickup</p>
            <p class="font-display text-3xl font-semibold text-emerald-700 mt-1">{{ $stats['ready'] }}</p>
        </div>
    </div>

    @if($filters['unassigned'] ?? false)
        <x-alert type="warning" class="mb-6">
            Showing unassigned jobs only — accepted orders waiting for a technician.
            <a href="{{ route('admin.workshop.index') }}" class="underline font-medium ml-1">Show full queue</a>
        </x-alert>
    @endif

    @if($technicians->isEmpty())
        <x-alert type="warning" class="mb-6">
            No technician accounts exist yet.
            @can('permission', 'users.manage')
                <a href="{{ route('admin.users.create') }}" class="underline font-medium ml-1">Create a technician account</a>
            @endcan
            before assigning production work.
        </x-alert>
    @endif

    <form method="GET" class="mb-6 flex flex-col lg:flex-row gap-3">
        @if($filters['unassigned'] ?? false)
            <input type="hidden" name="unassigned" value="1">
        @endif
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
            placeholder="Search order # or customer name..."
            maxlength="100"
            class="jewel-input lg:flex-1">
        <select name="status" class="jewel-input lg:w-44">
            <option value="">All statuses</option>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="technician_id" class="jewel-input lg:w-48">
            <option value="">All technicians</option>
            @foreach($technicians as $technician)
                <option value="{{ $technician->id }}" @selected(($filters['technician_id'] ?? '') == $technician->id)>
                    {{ $technician->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="jewel-btn px-6">Filter</button>
        @if(!empty(array_filter($filters ?? [])))
            <a href="{{ route('admin.workshop.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm text-gray-500 hover:text-jewel-dark">Clear</a>
        @endif
    </form>

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Due</th>
                        <th>Production</th>
                        <th>Technician</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr @class([
                            'transition',
                            'bg-amber-50 hover:bg-amber-100/80 border-l-4 border-l-amber-500' => $order->isDeliveryOverdue(),
                            'bg-sky-50 hover:bg-sky-100/70 border-l-4 border-l-sky-400' => $order->isDeliveryDueSoon(),
                            'hover:bg-jewel-cream/30' => ! $order->isDeliveryOverdue() && ! $order->isDeliveryDueSoon(),
                        ])>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-jewel-dark">{{ $order->order_number }}</span>
                                @if($order->isDeliveryOverdue())
                                    <span class="mt-1 block text-[10px] font-bold uppercase text-amber-800">Overdue</span>
                                @elseif($order->isDeliveryDueSoon())
                                    <span class="mt-1 block text-[10px] font-bold uppercase text-sky-800">Due soon</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $order->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->item_type_label }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->expected_delivery_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-production-status-badge :status="$order->production_status" />
                                <div class="mt-1"><x-task-status-badge :status="$order->task_status" /></div>
                            </td>
                            <td class="px-6 py-4">
                                @if(auth()->user()->isAdmin())
                                    @if($order->isAssignableToTechnician() || $order->assignedTechnician)
                                        <form method="POST" action="{{ route('admin.orders.assign-technician', $order) }}" class="flex items-center gap-2 min-w-[12rem]">
                                            @csrf
                                            @method('PATCH')
                                            <select name="assigned_technician_id" class="jewel-input text-xs py-1.5" onchange="this.form.submit()">
                                                <option value="">Unassigned</option>
                                                @foreach($technicians as $technician)
                                                    <option value="{{ $technician->id }}" @selected($order->assigned_technician_id == $technician->id)>
                                                        {{ $technician->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <span class="text-sm text-slate-400">—</span>
                                    @endif
                                @else
                                    @if($order->assignedTechnician)
                                        <a href="{{ route('admin.workshop.technicians.show', $order->assignedTechnician) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">
                                            {{ $order->assignedTechnician->name }}
                                        </a>
                                    @else
                                        <span class="text-sm text-slate-400">Unassigned</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No production jobs match your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-jewel-gold/10">{{ $orders->links() }}</div>
        @endif
    </div>
</x-admin-layout>
