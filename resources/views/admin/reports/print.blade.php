<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .print-page { box-shadow: none !important; border: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-700 text-sm">
    <div class="no-print max-w-5xl mx-auto px-4 py-6 flex justify-between items-center">
        <a href="{{ route('admin.reports.show', array_merge(['type' => $type], request()->query())) }}" class="text-jewel-gold-dark hover:underline">&larr; Back to report</a>
        <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Print / Save as PDF</button>
    </div>

    <div class="print-page max-w-5xl mx-auto bg-white shadow-lg border border-slate-200 rounded-xl p-8 sm:p-12 mb-12">
        <div class="border-b border-slate-200 pb-6 mb-6">
            <p class="text-xs uppercase tracking-widest text-slate-400">Rajabharana Jewellery</p>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $report['title'] }}</h1>
            <p class="text-slate-500 mt-1">{{ $report['description'] }}</p>
            <div class="flex flex-wrap gap-4 mt-4 text-xs text-slate-500">
                @if($report['date_from'] && $report['date_to'])
                    <span>Period: <strong>{{ $report['date_from'] }}</strong> to <strong>{{ $report['date_to'] }}</strong></span>
                @endif
                <span>Generated: <strong>{{ $report['generated_at']->format('M d, Y H:i') }}</strong></span>
                <span>By: <strong>{{ $generatedBy }}</strong></span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-8">
            @foreach($report['kpis'] as $kpi)
                <div class="border border-slate-200 rounded-lg p-3 text-center">
                    <p class="text-[10px] uppercase tracking-wide text-slate-400">{{ $kpi['label'] }}</p>
                    <p class="text-base font-bold text-slate-800 mt-0.5">{{ $kpi['value'] }}</p>
                </div>
            @endforeach
        </div>

        @include('admin.reports.partials.print-table', [
            'title' => $type === \App\Enums\ReportType::Inventory ? 'Category Summary' : null,
            'columns' => $report['columns'],
            'rows' => $report['rows'],
        ])

        @foreach($report['sections'] ?? [] as $section)
            @include('admin.reports.partials.print-table', [
                'title' => $section['title'],
                'columns' => $section['columns'],
                'rows' => $section['rows'],
            ])
        @endforeach
    </div>
</body>
</html>
