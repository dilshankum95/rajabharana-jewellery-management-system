<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="jewel-page-title text-xl">Raw Materials</h1>
                <p class="jewel-page-subtitle">Workshop stock — gold, silver, gemstones, and components</p>
            </div>
            @can('permission', 'raw-materials.manage')
                <a href="{{ route('admin.raw-materials.create') }}" class="jewel-btn">+ Add Material</a>
            @endcan
        </div>
    </x-slot>

    @if($lowStockCount > 0)
        <x-alert type="warning" class="mb-6">
            {{ $lowStockCount }} material(s) are at or below reorder level.
            <a href="{{ route('admin.raw-materials.index', ['low_stock' => 1]) }}" class="font-semibold underline ml-1">View low stock</a>
        </x-alert>
    @endif

    <form method="GET" class="mb-6 flex flex-col lg:flex-row gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
            placeholder="Search by name or code..."
            maxlength="100"
            class="jewel-input lg:flex-1">
        <select name="material_type" class="jewel-input lg:w-44">
            <option value="">All types</option>
            @foreach($materialTypes as $value => $label)
                <option value="{{ $value }}" @selected(($filters['material_type'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <label class="inline-flex items-center gap-2 px-3 text-sm text-stone-600">
            <input type="checkbox" name="low_stock" value="1" @checked($filters['low_stock'] ?? false) class="rounded border-stone-300 text-jewel-rose focus:ring-jewel-rose/20">
            Low stock only
        </label>
        <button type="submit" class="jewel-btn px-6">Search</button>
        @if(!empty(array_filter($filters ?? [])))
            <a href="{{ route('admin.raw-materials.index') }}" class="inline-flex items-center justify-center px-4 text-sm text-stone-500 hover:text-stone-700">Clear</a>
        @endif
    </form>

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Stock</th>
                        <th>Reorder Level</th>
                        <th>Unit Cost</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                        <tr @class(['bg-amber-50/50' => $material->isLowStock()])>
                            <td class="font-mono text-xs text-stone-600">{{ $material->code }}</td>
                            <td class="font-medium text-stone-700">{{ $material->name }}</td>
                            <td>{{ $material->material_type_label }}</td>
                            <td>
                                <span class="font-semibold">{{ number_format($material->stock_quantity, 3) }}</span>
                                <span class="text-xs text-stone-400">{{ $material->unit_label }}</span>
                            </td>
                            <td>
                                @if($material->reorder_level !== null)
                                    {{ number_format($material->reorder_level, 3) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $material->unit_cost ? number_format($material->unit_cost, 2) : '—' }}</td>
                            <td>
                                @if($material->is_active)
                                    <span class="jewel-badge-active">Active</span>
                                @else
                                    <span class="jewel-badge-inactive">Inactive</span>
                                @endif
                                @if($material->isLowStock())
                                    <span class="ml-1 text-xs font-medium text-amber-700">Low</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                @can('permission', 'raw-materials.manage')
                                    <a href="{{ route('admin.raw-materials.edit', $material) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">Edit</a>
                                @else
                                    <span class="text-xs text-stone-400">View only</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-stone-400">
                                No raw materials found.
                                @can('permission', 'raw-materials.manage')
                                    <a href="{{ route('admin.raw-materials.create') }}" class="jewel-link ml-1">Add the first material</a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->hasPages())
            <div class="px-6 py-4 border-t border-stone-100">{{ $materials->links() }}</div>
        @endif
    </div>
</x-admin-layout>
