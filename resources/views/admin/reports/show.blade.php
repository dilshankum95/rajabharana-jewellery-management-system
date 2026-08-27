<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('admin.reports.index') }}" class="text-sm text-jewel-gold-dark hover:text-jewel-gold">&larr; All Reports</a>
                <h1 class="jewel-page-title text-xl mt-1">{{ $report['title'] }}</h1>
                <p class="jewel-page-subtitle">{{ $report['description'] }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.reports.print', array_merge(['type' => $type], request()->query())) }}"
                    target="_blank" class="jewel-btn-outline text-sm px-4 py-2">Print / PDF</a>
                @can('permission', 'reports.export')
                    <a href="{{ route('admin.reports.export', array_merge(['type' => $type], request()->query())) }}"
                        class="jewel-btn text-sm px-4 py-2">Export CSV</a>
                @endcan
            </div>
        </div>
    </x-slot>

    @if($type->usesDateRange())
        <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3 items-end">
            <div>
                <label for="date_from" class="jewel-label">From</label>
                <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? $report['date_from'] }}"
                    class="jewel-input mt-1">
            </div>
            <div>
                <label for="date_to" class="jewel-label">To</label>
                <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? $report['date_to'] }}"
                    class="jewel-input mt-1">
            </div>
            <button type="submit" class="jewel-btn px-6">Apply</button>
            @if(!empty(array_filter($filters ?? [])))
                <a href="{{ route('admin.reports.show', $type) }}" class="text-sm text-gray-500 hover:text-jewel-dark py-2">Reset</a>
            @endif
        </form>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        @foreach($report['kpis'] as $kpi)
            <div class="jewel-card p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 font-medium">{{ $kpi['label'] }}</p>
                <p class="text-lg font-bold text-jewel-dark mt-1">{{ $kpi['value'] }}</p>
            </div>
        @endforeach
    </div>

    @include('admin.reports.partials.table', [
        'title' => $type === \App\Enums\ReportType::Inventory ? 'Category Summary' : 'Report Data',
        'columns' => $report['columns'],
        'rows' => $report['rows'],
        'generatedAt' => $report['generated_at'],
        'showGeneratedAt' => empty($report['sections']),
    ])

    @foreach($report['sections'] ?? [] as $section)
        @include('admin.reports.partials.table', [
            'title' => $section['title'],
            'columns' => $section['columns'],
            'rows' => $section['rows'],
            'generatedAt' => $report['generated_at'],
            'showGeneratedAt' => $loop->last,
        ])
    @endforeach
</x-admin-layout>
