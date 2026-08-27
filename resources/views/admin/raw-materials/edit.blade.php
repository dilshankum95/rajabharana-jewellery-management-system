<x-admin-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.raw-materials.index') }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold transition">&larr; Back to raw materials</a>
            <h1 class="jewel-page-title text-xl mt-2">Edit Raw Material</h1>
            <p class="jewel-page-subtitle">{{ $material->code }} — {{ $material->name }}</p>
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 jewel-card p-6">
            <form method="POST" action="{{ route('admin.raw-materials.update', $material) }}">
                @csrf @method('PATCH')
                @include('admin.raw-materials._form', ['material' => $material, 'materialTypes' => $materialTypes, 'stockUnits' => $stockUnits])
                <div class="mt-8 flex gap-3">
                    <button type="submit" class="jewel-btn">Update Material</button>
                    <a href="{{ route('admin.raw-materials.index') }}" class="jewel-btn-outline">Cancel</a>
                </div>
            </form>
        </div>

        @can('permission', 'raw-materials.manage')
            <div class="space-y-6">
                <div class="jewel-card p-6">
                    <h2 class="jewel-section-title mb-4">Quick Stock Adjustment</h2>
                    <form method="POST" action="{{ route('admin.raw-materials.adjust-stock', $material) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="quantity_delta" class="jewel-label">Add / Remove Amount</label>
                            <input id="quantity_delta" name="quantity_delta" type="number" step="0.001" required
                                placeholder="e.g. 50 or -10"
                                class="jewel-input mt-1.5">
                            <p class="mt-1 text-xs text-stone-400">Use negative values to deduct (workshop usage).</p>
                            <x-input-error :messages="$errors->get('quantity_delta')" />
                        </div>
                        <div>
                            <label for="note" class="jewel-label">Note</label>
                            <input id="note" name="note" type="text" maxlength="255"
                                placeholder="e.g. Received from supplier"
                                class="jewel-input mt-1.5">
                        </div>
                        <button type="submit" class="jewel-btn w-full">Adjust Stock</button>
                    </form>
                    <p class="mt-3 text-sm text-stone-500">Current: <strong>{{ number_format($material->stock_quantity, 3) }} {{ $material->unit_label }}</strong></p>
                </div>

                @if($material->stockMovements->isNotEmpty())
                    <div class="jewel-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h2 class="font-semibold text-jewel-dark">Recent Movements</h2>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach($material->stockMovements as $movement)
                                <li class="px-6 py-3 text-sm">
                                    <div class="flex justify-between gap-2">
                                        <span @class(['font-semibold', 'text-emerald-700' => $movement->quantity_delta > 0, 'text-rose-700' => $movement->quantity_delta < 0])>
                                            {{ $movement->quantity_delta > 0 ? '+' : '' }}{{ number_format($movement->quantity_delta, 3) }}
                                        </span>
                                        <span class="text-stone-400 text-xs">{{ $movement->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <p class="text-stone-500 text-xs mt-0.5">{{ $movement->reason->label() }}</p>
                                    @if($movement->note)
                                        <p class="text-stone-400 text-xs">{{ $movement->note }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endcan
    </div>
</x-admin-layout>
