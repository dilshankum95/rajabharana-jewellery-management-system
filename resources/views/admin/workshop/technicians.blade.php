<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('admin.workshop.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to workshop queue</a>
                <h1 class="jewel-page-title text-xl mt-1">Technicians</h1>
                <p class="jewel-page-subtitle">Workshop team workload overview</p>
            </div>
            @can('permission', 'users.manage')
                <a href="{{ route('admin.users.create') }}" class="jewel-btn">+ Add Technician</a>
            @endcan
        </div>
    </x-slot>

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Active Jobs</th>
                        <th>Ready (Recent)</th>
                        <th>Overdue</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($technicians as $technician)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $technician->name }}</td>
                            <td>{{ $technician->email }}</td>
                            <td>
                                <span @class([
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
                                    'bg-violet-100 text-violet-800' => $technician->active_jobs_count > 0,
                                    'bg-slate-100 text-slate-500' => $technician->active_jobs_count === 0,
                                ])>
                                    {{ $technician->active_jobs_count }}
                                </span>
                            </td>
                            <td class="text-slate-600">{{ $technician->ready_jobs_count }}</td>
                            <td>
                                @if($technician->overdue_jobs_count > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-900">
                                        {{ $technician->overdue_jobs_count }}
                                    </span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.workshop.technicians.show', $technician) }}" class="text-sm font-semibold text-jewel-gold-dark hover:text-jewel-gold">View workload</a>
                                @can('permission', 'users.manage')
                                    <a href="{{ route('admin.users.edit', $technician) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 ml-3">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                No technicians yet.
                                @can('permission', 'users.manage')
                                    <a href="{{ route('admin.users.create') }}" class="text-jewel-gold-dark hover:text-jewel-gold font-medium">Create one</a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
