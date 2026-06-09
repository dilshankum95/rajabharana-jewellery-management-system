<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="jewel-page-title text-xl">Jewellery Catalog</h1>
                <p class="jewel-page-subtitle">Manage catalog items for customer orders</p>
            </div>
            <a href="{{ route('admin.catalog.create') }}" class="jewel-btn">+ Add Item</a>
        </div>
    </x-slot>

    <form method="GET" class="mb-6 flex flex-col lg:flex-row gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
            placeholder="Search by name, code, or category..."
            class="jewel-input lg:flex-1">
        <select name="category" class="jewel-input lg:w-44">
            <option value="">All categories</option>
            @foreach($categories as $value => $label)
                <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="availability_status" class="jewel-input lg:w-44">
            <option value="">All statuses</option>
            @foreach($availabilityStatuses as $value => $label)
                <option value="{{ $value }}" @selected(($filters['availability_status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="jewel-btn px-6">Search</button>
        @if(!empty(array_filter($filters ?? [])))
            <a href="{{ route('admin.catalog.index') }}" class="inline-flex items-center justify-center px-4 text-sm text-stone-500 hover:text-stone-700">Clear</a>
        @endif
    </form>

    <div class="jewel-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="jewel-table min-w-full">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Gold</th>
                        <th>Weight</th>
                        <th>Price (LKR)</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($designs as $design)
                        <tr>
                            <td>
                                @if($design->image_url)
                                    <img src="{{ $design->image_url }}" alt="" class="w-12 h-12 rounded-lg object-cover border border-stone-200">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-stone-100 flex items-center justify-center">
                                        <x-jewellery-logo class="w-6 h-6 text-stone-300" />
                                    </div>
                                @endif
                            </td>
                            <td class="font-mono text-xs text-stone-600">{{ $design->code }}</td>
                            <td>
                                <p class="font-medium text-stone-700">{{ $design->name }}</p>
                                @if($design->orders_count > 0)
                                    <p class="text-xs text-stone-400">{{ $design->orders_count }} orders</p>
                                @endif
                            </td>
                            <td>{{ $design->category_label }}</td>
                            <td>{{ $design->gold_quality_label }}</td>
                            <td>{{ number_format($design->weight_grams, 2) }}g</td>
                            <td class="font-medium">{{ number_format($design->selling_price, 2) }}</td>
                            <td>
                                <span class="{{ $design->availability_status->badgeClass() }}">
                                    {{ $design->availability_status->label() }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.catalog.edit', $design) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">Edit</a>
                                @if($design->orders_count === 0)
                                    <form method="POST" action="{{ route('admin.catalog.destroy', $design) }}" class="inline ml-2"
                                        onsubmit="return confirm('Delete this catalog item?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm text-rose-600 hover:text-rose-800">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-stone-400">
                                No catalog items found.
                                <a href="{{ route('admin.catalog.create') }}" class="jewel-link ml-1">Add the first item</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($designs->hasPages())
            <div class="px-6 py-4 border-t border-stone-100">{{ $designs->links() }}</div>
        @endif
    </div>
</x-admin-layout>
