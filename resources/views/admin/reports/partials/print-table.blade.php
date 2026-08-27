@props([
    'title' => null,
    'columns' => [],
    'rows' => [],
])

<div class="{{ $title ? 'mt-8' : '' }}">
    @if($title)
        <h2 class="text-sm font-bold text-slate-800 mb-3">{{ $title }}</h2>
    @endif
    <table class="w-full text-left border-collapse text-xs">
        <thead>
            <tr class="border-b-2 border-slate-300">
                @foreach($columns as $column)
                    <th class="py-2 pr-3 font-semibold text-slate-700">{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr @class([
                    'border-b border-slate-100',
                    'bg-slate-50 font-semibold' => ($row[0] ?? '') === 'Grand Total',
                ])>
                    @foreach($row as $cell)
                        <td class="py-2 pr-3">{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" class="py-8 text-center text-slate-400">No data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
