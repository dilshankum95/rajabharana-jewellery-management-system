@props([
    'title' => 'Report Data',
    'columns' => [],
    'rows' => [],
    'generatedAt' => null,
    'showGeneratedAt' => false,
])

<div class="jewel-card overflow-hidden mb-6 last:mb-0">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
        <h2 class="font-semibold text-jewel-dark">{{ $title }}</h2>
        @if($showGeneratedAt && $generatedAt)
            <p class="text-xs text-gray-400">Generated {{ $generatedAt->format('M d, Y H:i') }}</p>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="jewel-table min-w-full">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr @class([
                        'hover:bg-jewel-cream/30',
                        'bg-jewel-cream/50 font-semibold' => ($row[0] ?? '') === 'Grand Total',
                    ])>
                        @foreach($row as $cell)
                            <td class="px-6 py-3 text-sm whitespace-nowrap">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-12 text-center text-gray-400">
                            No data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
