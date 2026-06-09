<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="jewel-page-title text-xl">Place a New Order</h1>
            <p class="jewel-page-subtitle">Submit a catalog design or your own custom jewellery design</p>
        </div>
    </x-slot>

    <div class="py-8" x-data="orderForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Design Choice --}}
                <section class="jewel-card jewel-card-body">
                    <h3 class="jewel-section-title mb-1">1. Design Choice</h3>
                    <p class="text-sm text-gray-500 mb-6">Choose from our catalog or submit your own custom design</p>

                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        @foreach($designTypes as $value => $label)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="design_type" value="{{ $value }}"
                                    x-model="designType"
                                    class="peer sr-only"
                                    {{ old('design_type', 'catalog') === $value ? 'checked' : '' }}>
                                <div class="rounded-xl border-2 border-jewel-gold/20 p-4 peer-checked:border-jewel-gold peer-checked:bg-jewel-cream/50 transition">
                                    <p class="font-medium text-jewel-dark">{{ $label }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        @if($value === 'catalog')
                                            Select from Rajabharana's curated collection
                                        @else
                                            Upload your reference image and specifications
                                        @endif
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('design_type')" />

                    {{-- Catalog designs --}}
                    <div x-show="designType === 'catalog'" x-cloak class="space-y-4">
                        <label class="jewel-label">Browse by Category</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="selectedCategory = 'all'"
                                :class="selectedCategory === 'all'
                                    ? 'bg-btn-gradient text-white shadow-jewel border-transparent'
                                    : 'bg-white text-slate-600 border-slate-200 hover:border-jewel-rose/40 hover:bg-jewel-rose-light'"
                                class="px-4 py-2 rounded-xl text-sm font-semibold border transition">
                                All Items
                                <span class="ml-1 opacity-75">({{ $catalogDesigns->count() }})</span>
                            </button>
                            @foreach($categories as $value => $label)
                                <button type="button" @click="selectedCategory = '{{ $value }}'"
                                    :class="selectedCategory === '{{ $value }}'
                                        ? 'bg-btn-gradient text-white shadow-jewel border-transparent'
                                        : 'bg-white text-slate-600 border-slate-200 hover:border-jewel-rose/40 hover:bg-jewel-rose-light'"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold border transition">
                                    {{ $label }}
                                    <span class="ml-1 opacity-75">({{ $catalogDesigns->where('category', $value)->count() }})</span>
                                </button>
                            @endforeach
                        </div>

                        <label class="jewel-label pt-2">Select Catalog Design *</label>
                        @if($catalogDesigns->isEmpty())
                            <x-empty-state title="No catalog items available" description="Please check back later or submit a custom design." />
                        @else
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($catalogDesigns as $design)
                                    <label class="cursor-pointer"
                                        x-show="selectedCategory === 'all' || selectedCategory === '{{ $design->category }}'"
                                        x-transition>
                                        <input type="radio" name="catalog_design_id" value="{{ $design->id }}"
                                            class="peer sr-only"
                                            @change="selectCatalogDesign('{{ $design->category }}', '{{ $design->gold_quality }}', {{ $design->weight_grams }})"
                                            {{ old('catalog_design_id', $preselectedCatalogId ?? '') == $design->id ? 'checked' : '' }}>
                                        <div class="rounded-xl border-2 border-slate-200 overflow-hidden peer-checked:border-jewel-rose peer-checked:ring-2 peer-checked:ring-jewel-rose/20 transition hover:border-jewel-gold/30 hover:shadow-jewel">
                                            <div class="h-32 bg-slate-50 flex items-center justify-center relative">
                                                @if($design->image_url)
                                                    <img src="{{ $design->image_url }}" alt="{{ $design->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <x-jewellery-logo class="w-10 h-10 text-jewel-gold/30" />
                                                @endif
                                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-white/90 text-[10px] font-semibold uppercase tracking-wide text-jewel-rose-dark">
                                                    {{ $design->category_label }}
                                                </span>
                                            </div>
                                            <div class="p-3">
                                                <p class="font-medium text-sm text-slate-800">{{ $design->name }}</p>
                                                <p class="text-xs text-slate-400">{{ $design->code }}</p>
                                                <p class="text-xs text-slate-500 mt-1">{{ $design->gold_quality_label }} · {{ number_format($design->weight_grams, 1) }}g</p>
                                                <p class="text-sm font-semibold text-jewel-gold-dark mt-1">LKR {{ number_format($design->selling_price, 0) }}</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p x-show="!hasVisibleDesigns()" x-cloak class="text-sm text-slate-500 text-center py-8">
                                No items in this category. Try another category or choose "All Items".
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('catalog_design_id')" />
                    </div>

                    {{-- Reference image (shared) --}}
                    <div class="mt-4">
                        <label class="jewel-label">
                            <span x-text="designType === 'custom' ? 'Reference Image *' : 'Additional Reference Image (optional)'"></span>
                        </label>
                        <input type="file" name="reference_image" accept="image/jpeg,image/png,image/webp"
                            :required="designType === 'custom'"
                            class="mt-1.5 jewel-file-input">
                        <p class="mt-1 text-xs text-gray-400" x-text="designType === 'custom'
                            ? 'Upload a sketch, photo, or inspiration image (max 5MB)'
                            : 'Upload modifications or inspiration for the catalog design (max 5MB)'"></p>
                        <x-input-error :messages="$errors->get('reference_image')" />
                    </div>
                </section>

                {{-- Item Details --}}
                <section class="jewel-card jewel-card-body">
                    <h3 class="jewel-section-title mb-1">2. Item Details</h3>
                    <p class="text-sm text-gray-500 mb-6">Specify the type, size, and weight of your jewellery piece</p>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="item_type" class="jewel-label">Item Type *</label>
                            <select id="item_type" name="item_type" required class="jewel-input mt-1.5">
                                <option value="">Select type</option>
                                @foreach($itemTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('item_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('item_type')" />
                        </div>

                        <div>
                            <label for="item_name" class="jewel-label">Piece Name (optional)</label>
                            <input id="item_name" name="item_name" type="text" value="{{ old('item_name') }}"
                                maxlength="255"
                                placeholder="e.g. Wedding Ring, Mother's Day Gift"
                                class="jewel-input mt-1.5">
                            <x-input-error :messages="$errors->get('item_name')" />
                        </div>

                        <div>
                            <label for="size" class="jewel-label">Size</label>
                            <input id="size" name="size" type="text" value="{{ old('size') }}"
                                maxlength="100"
                                placeholder="Ring size, chain length (inches/cm), etc."
                                class="jewel-input mt-1.5">
                            <x-input-error :messages="$errors->get('size')" />
                        </div>

                        <div>
                            <label for="weight_grams" class="jewel-label">Estimated Weight (grams)</label>
                            <input id="weight_grams" name="weight_grams" type="number" step="0.01" min="0.01" max="99999"
                                value="{{ old('weight_grams') }}" placeholder="e.g. 8.5"
                                class="jewel-input mt-1.5">
                            <x-input-error :messages="$errors->get('weight_grams')" />
                        </div>

                        <div>
                            <label for="quantity" class="jewel-label">Quantity *</label>
                            <input id="quantity" name="quantity" type="number" min="1" max="50"
                                value="{{ old('quantity', 1) }}" required class="jewel-input mt-1.5">
                            <x-input-error :messages="$errors->get('quantity')" />
                        </div>

                        <div>
                            <label for="gold_quality" class="jewel-label">Preferred Gold Quality *</label>
                            <select id="gold_quality" name="gold_quality" required class="jewel-input mt-1.5">
                                <option value="">Select gold quality</option>
                                @foreach($goldQualities as $value => $label)
                                    <option value="{{ $value }}" @selected(old('gold_quality') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('gold_quality')" />
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="specifications" class="jewel-label">Specifications & Details</label>
                        <textarea id="specifications" name="specifications" rows="3" maxlength="2000" minlength="3"
                            placeholder="Describe engravings, finish (matte/polish), width, thickness, or any specific requirements..."
                            class="jewel-input mt-1.5">{{ old('specifications') }}</textarea>
                        <x-input-error :messages="$errors->get('specifications')" />
                    </div>
                </section>

                {{-- Gemstones --}}
                <section class="jewel-card jewel-card-body">
                    <h3 class="jewel-section-title mb-1">3. Gemstones (Optional)</h3>
                    <p class="text-sm text-gray-500 mb-6">Include gemstone preferences if applicable</p>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="gemstone_type" class="jewel-label">Gemstone Type</label>
                            <input id="gemstone_type" name="gemstone_type" type="text" value="{{ old('gemstone_type') }}"
                                maxlength="100"
                                placeholder="e.g. Ruby, Sapphire, Diamond, Pearl"
                                class="jewel-input mt-1.5">
                            <x-input-error :messages="$errors->get('gemstone_type')" />
                        </div>
                        <div class="sm:col-span-1">
                            <label for="gemstone_details" class="jewel-label">Gemstone Details</label>
                            <textarea id="gemstone_details" name="gemstone_details" rows="2" maxlength="1000" minlength="3"
                                placeholder="Carat weight, cut, color, setting style..."
                                class="jewel-input mt-1.5">{{ old('gemstone_details') }}</textarea>
                            <x-input-error :messages="$errors->get('gemstone_details')" />
                        </div>
                    </div>
                </section>

                {{-- Delivery & Contact --}}
                <section class="jewel-card jewel-card-body">
                    <h3 class="jewel-section-title mb-1">4. Delivery & Contact</h3>
                    <p class="text-sm text-gray-500 mb-6">When do you need it and how can we reach you?</p>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="expected_delivery_date" class="jewel-label">Expected Delivery Date *</label>
                            <input id="expected_delivery_date" name="expected_delivery_date" type="date"
                                value="{{ old('expected_delivery_date') }}"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                max="{{ now()->addYear()->format('Y-m-d') }}"
                                required class="jewel-input mt-1.5">
                            <x-input-error :messages="$errors->get('expected_delivery_date')" />
                        </div>

                        <div>
                            <label for="contact_phone" class="jewel-label">Contact Phone *</label>
                            <input id="contact_phone" name="contact_phone" type="tel"
                                value="{{ old('contact_phone', Auth::user()->phone) }}"
                                required minlength="7" maxlength="25" pattern="[\+]?[0-9\s\-().]{7,25}"
                                class="jewel-input mt-1.5">
                            <x-input-error :messages="$errors->get('contact_phone')" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="delivery_address" class="jewel-label">Delivery Address</label>
                            <textarea id="delivery_address" name="delivery_address" rows="2" maxlength="500" minlength="5"
                                placeholder="Full delivery address (optional if picking up in store)"
                                class="jewel-input mt-1.5">{{ old('delivery_address', Auth::user()->address) }}</textarea>
                            <x-input-error :messages="$errors->get('delivery_address')" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="special_instructions" class="jewel-label">Special Instructions</label>
                            <textarea id="special_instructions" name="special_instructions" rows="2" maxlength="2000" minlength="3"
                                placeholder="Gift wrapping, urgency notes, preferred contact time..."
                                class="jewel-input mt-1.5">{{ old('special_instructions') }}</textarea>
                            <x-input-error :messages="$errors->get('special_instructions')" />
                        </div>
                    </div>
                </section>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-jewel-dark transition">Cancel</a>
                    <button type="submit" class="jewel-btn w-full sm:w-auto px-10">
                        Submit Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function orderForm() {
            const categoryToItemType = {
                ring: 'ring',
                necklace: 'necklace',
                bracelet: 'bracelet',
                earring: 'earrings',
                chain: 'chain',
                pendant: 'pendant',
                bangle: 'bangle',
                anklet: 'anklet',
                other: 'other',
            };

            return {
                designType: @json(old('design_type', 'catalog')),
                selectedCategory: @json(
                    old('catalog_design_id', $preselectedCatalogId ?? null)
                        ? ($catalogDesigns->firstWhere('id', (int) old('catalog_design_id', $preselectedCatalogId ?? 0))?->category ?? 'all')
                        : 'all'
                ),
                designCategories: @json($catalogDesigns->pluck('category', 'id')),
                hasVisibleDesigns() {
                    if (this.selectedCategory === 'all') {
                        return true;
                    }
                    return Object.values(this.designCategories).includes(this.selectedCategory);
                },
                selectCatalogDesign(category, goldQuality, weightGrams) {
                    const itemType = document.getElementById('item_type');
                    const goldField = document.getElementById('gold_quality');
                    const weightField = document.getElementById('weight_grams');

                    if (itemType && categoryToItemType[category]) {
                        itemType.value = categoryToItemType[category];
                    }
                    if (goldField && goldQuality) {
                        goldField.value = goldQuality;
                    }
                    if (weightField && weightGrams) {
                        weightField.value = weightGrams;
                    }
                },
            };
        }
    </script>
</x-app-layout>
