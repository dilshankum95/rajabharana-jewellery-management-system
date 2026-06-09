<x-public-layout>
    <x-slot name="title">Our Catalog — {{ config('app.name') }}</x-slot>

    <div class="px-6 py-10 sm:px-10 lg:px-16 max-w-7xl mx-auto">
        <div class="text-center mb-10 animate-slide-up">
            <span class="inline-block px-4 py-1.5 rounded-full bg-jewel-rose/20 text-jewel-rose-light text-xs font-semibold uppercase tracking-widest mb-4 border border-jewel-rose/30">Browse Collection</span>
            <h1 class="font-display text-3xl sm:text-4xl font-semibold text-white">Our Jewellery Catalog</h1>
            <p class="mt-3 text-slate-400 max-w-xl mx-auto">Explore handcrafted gold pieces. Sign in or create an account when you're ready to order.</p>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('catalog.index') }}" class="jewel-card p-4 sm:p-5 mb-8 flex flex-col lg:flex-row gap-3">
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Search by name or item code..."
                maxlength="255"
                class="jewel-input flex-1">
            <select name="category" class="jewel-input lg:w-48">
                <option value="">All Categories</option>
                @foreach($categories as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="jewel-btn px-6">Search</button>
                @if(($filters['search'] ?? '') || ($filters['category'] ?? ''))
                    <a href="{{ route('catalog.index') }}" class="jewel-btn-outline px-4">Clear</a>
                @endif
            </div>
        </form>

        @if($designs->isEmpty())
            <x-empty-state title="No items found" description="Try a different category or check back soon for new designs." class="jewel-card py-16">
                <a href="{{ route('catalog.index') }}" class="jewel-btn">View All Items</a>
            </x-empty-state>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($designs as $design)
                    <div class="jewel-card overflow-hidden group hover:shadow-jewel-lg transition flex flex-col">
                        <a href="{{ route('catalog.show', $design) }}" class="block flex-1">
                            <div class="h-48 bg-slate-50 flex items-center justify-center relative overflow-hidden">
                                @if($design->image_url)
                                    <img src="{{ $design->image_url }}" alt="{{ $design->name }}"
                                        class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <x-jewellery-logo class="w-12 h-12 text-jewel-gold/30" />
                                @endif
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-white/90 text-[10px] font-semibold uppercase tracking-wide text-jewel-rose-dark">
                                    {{ $design->category_label }}
                                </span>
                            </div>
                            <div class="p-4">
                                <p class="font-semibold text-slate-800 group-hover:text-jewel-rose-dark transition">{{ $design->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $design->code }}</p>
                                <p class="text-xs text-slate-500 mt-2">{{ $design->gold_quality_label }} · {{ number_format($design->weight_grams, 1) }}g</p>
                                <p class="text-sm font-semibold text-jewel-gold-dark mt-2">LKR {{ number_format($design->selling_price, 0) }}</p>
                            </div>
                        </a>
                        @guest
                            <div class="px-4 pb-4">
                                <a href="{{ route('purchase', $design) }}" class="jewel-btn w-full text-center text-sm py-2.5 block">
                                    Register to Order
                                </a>
                            </div>
                        @endguest
                    </div>
                @endforeach
            </div>

            @if($designs->hasPages())
                <div class="mt-10">{{ $designs->links() }}</div>
            @endif
        @endif

        @guest
            <div class="mt-12 text-center jewel-card p-8 bg-white/5 border-white/10">
                <h2 class="font-display text-xl font-semibold text-white">Ready to order?</h2>
                <p class="text-slate-400 mt-2 text-sm">Create a free account to place an order from our catalog or submit a custom design.</p>
                <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('register') }}" class="jewel-btn px-8">Create Account</a>
                    <a href="{{ route('login') }}" class="jewel-btn px-8">Sign In</a>
                </div>
            </div>
        @endguest
    </div>
</x-public-layout>
