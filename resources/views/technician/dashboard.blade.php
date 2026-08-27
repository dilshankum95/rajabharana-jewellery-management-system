<x-technician-layout>
    <x-slot name="header">
        <div>
            <h1 class="jewel-page-title text-xl">My Jobs</h1>
            <p class="jewel-page-subtitle">Production work assigned to you</p>
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
            <p class="text-xs uppercase tracking-wider text-amber-600/80 font-semibold">Due Soon</p>
            <p class="font-display text-3xl font-semibold text-amber-700 mt-1">{{ $stats['due_soon'] }}</p>
        </div>
    </div>

    <section class="jewel-card jewel-card-body mb-8">
        <h2 class="jewel-section-title mb-4">Active Production</h2>

        @if($activeJobs->isEmpty())
            <p class="text-sm text-slate-500">No active jobs assigned yet. Check back when the administrator assigns work to you.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-slate-100">
                            <th class="pb-3 pr-4 font-medium">Order</th>
                            <th class="pb-3 pr-4 font-medium">Item</th>
                            <th class="pb-3 pr-4 font-medium">Task / Production</th>
                            <th class="pb-3 pr-4 font-medium">Due Date</th>
                            <th class="pb-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($activeJobs as $job)
                            <tr class="{{ $job->isDeliveryOverdue() ? 'bg-rose-50/50' : ($job->isDeliveryDueSoon() ? 'bg-amber-50/40' : '') }}">
                                <td class="py-3 pr-4 font-medium">{{ $job->order_number }}</td>
                                <td class="py-3 pr-4">{{ $job->item_type_label }}@if($job->item_name) · {{ $job->item_name }}@endif</td>
                                <td class="py-3 pr-4">
                                    <div class="flex flex-wrap gap-1">
                                        <x-task-status-badge :status="$job->task_status" />
                                        <x-production-status-badge :status="$job->production_status" />
                                    </div>
                                </td>
                                <td class="py-3 pr-4 {{ $job->isDeliveryOverdue() ? 'text-rose-600 font-medium' : ($job->isDeliveryDueSoon() ? 'text-amber-700 font-medium' : '') }}">
                                    {{ $job->expected_delivery_date->format('M d, Y') }}
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('technician.jobs.show', $job) }}" class="text-jewel-gold-dark hover:text-jewel-gold font-medium">Open &rarr;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @if($readyJobs->isNotEmpty())
        <section class="jewel-card jewel-card-body">
            <h2 class="jewel-section-title mb-4">Recently Completed (Ready to Pickup)</h2>
            <ul class="divide-y divide-slate-50 text-sm">
                @foreach($readyJobs as $job)
                    <li class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <span class="font-medium">{{ $job->order_number }}</span>
                            <span class="text-slate-400 mx-2">&middot;</span>
                            <span>{{ $job->item_type_label }}</span>
                        </div>
                        <a href="{{ route('technician.jobs.show', $job) }}" class="text-jewel-gold-dark hover:text-jewel-gold font-medium shrink-0">View</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-technician-layout>
