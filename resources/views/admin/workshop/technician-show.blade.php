<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('admin.workshop.technicians') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; All technicians</a>
                <h1 class="jewel-page-title text-xl mt-1">{{ $technician->name }}</h1>
                <p class="jewel-page-subtitle">{{ $technician->email }} · Workshop workload</p>
            </div>
            @can('permission', 'users.manage')
                <a href="{{ route('admin.users.edit', $technician) }}" class="jewel-btn-outline text-sm px-4 py-2">Edit Account</a>
            @endcan
        </div>
    </x-slot>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Active Jobs</p>
            <p class="font-display text-3xl font-semibold text-jewel-charcoal mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-violet-600/80 font-semibold">In Production</p>
            <p class="font-display text-3xl font-semibold text-violet-700 mt-1">{{ $stats['in_production'] }}</p>
        </div>
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-indigo-600/80 font-semibold">Quality Check</p>
            <p class="font-display text-3xl font-semibold text-indigo-700 mt-1">{{ $stats['quality_check'] }}</p>
        </div>
        <div class="jewel-card p-5">
            <p class="text-xs uppercase tracking-wider text-amber-600/80 font-semibold">Overdue</p>
            <p class="font-display text-3xl font-semibold text-amber-700 mt-1">{{ $stats['overdue'] }}</p>
        </div>
    </div>

    <section class="jewel-card jewel-card-body mb-8">
        <h2 class="jewel-section-title mb-4">Active Production</h2>

        @if($activeJobs->isEmpty())
            <p class="text-sm text-slate-500">No active jobs assigned to this technician.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-slate-100">
                            <th class="pb-3 pr-4 font-medium">Order</th>
                            <th class="pb-3 pr-4 font-medium">Item</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 pr-4 font-medium">Due</th>
                            <th class="pb-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($activeJobs as $job)
                            <tr @class([
                                'bg-rose-50/40' => $job->isDeliveryOverdue(),
                                'bg-amber-50/30' => $job->isDeliveryDueSoon(),
                            ])>
                                <td class="py-3 pr-4 font-medium">{{ $job->order_number }}</td>
                                <td class="py-3 pr-4">{{ $job->item_type_label }}</td>
                                <td class="py-3 pr-4"><x-order-status-badge :status="$job->status" /></td>
                                <td class="py-3 pr-4">{{ $job->expected_delivery_date->format('M d, Y') }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $job) }}" class="text-jewel-gold-dark hover:text-jewel-gold font-medium">Manage &rarr;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @if($recentReady->isNotEmpty())
        <section class="jewel-card jewel-card-body">
            <h2 class="jewel-section-title mb-4">Recently Completed (Ready)</h2>
            <ul class="divide-y divide-slate-50 text-sm">
                @foreach($recentReady as $job)
                    <li class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <span class="font-medium">{{ $job->order_number }}</span>
                            <span class="text-slate-400 mx-2">&middot;</span>
                            <span>{{ $job->item_type_label }}</span>
                            <span class="text-slate-400 ml-2">{{ $job->updated_at->format('M d, Y') }}</span>
                        </div>
                        <a href="{{ route('admin.orders.show', $job) }}" class="text-jewel-gold-dark hover:text-jewel-gold font-medium shrink-0">View</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-admin-layout>
