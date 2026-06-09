<x-public-layout>
    <x-slot name="title">{{ $design->name }} — {{ config('app.name') }}</x-slot>

    <div class="px-6 py-10 sm:px-10 lg:px-16 max-w-5xl mx-auto">
        <a href="{{ route('catalog.index') }}" class="inline-flex items-center text-sm font-medium text-jewel-gold hover:text-white transition mb-6">
            &larr; Back to catalog
        </a>

        <div class="grid lg:grid-cols-2 gap-8">
            {{-- Images --}}
            <div class="space-y-4">
                <div class="jewel-card overflow-hidden aspect-square bg-slate-50 flex items-center justify-center">
                    @if($design->image_url)
                        <img src="{{ $design->image_url }}" alt="{{ $design->name }}" class="w-full h-full object-cover">
                    @else
                        <x-jewellery-logo class="w-20 h-20 text-jewel-gold/30" />
                    @endif
                </div>
                @if($design->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($design->images as $image)
                            <div class="aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="jewel-card p-6 sm:p-8">
                <span class="inline-block px-3 py-1 rounded-full bg-jewel-rose-light text-jewel-rose-dark text-xs font-semibold uppercase tracking-wide">
                    {{ $design->category_label }}
                </span>
                <h1 class="font-display text-2xl sm:text-3xl font-semibold text-slate-800 mt-4">{{ $design->name }}</h1>
                <p class="text-sm text-slate-400 mt-1 font-mono">{{ $design->code }}</p>

                <p class="font-display text-3xl font-semibold text-jewel-gold-dark mt-6">
                    LKR {{ number_format($design->selling_price, 2) }}
                </p>

                <dl class="mt-6 space-y-3 text-sm border-t border-slate-100 pt-6">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Gold Quality</dt>
                        <dd class="font-medium text-slate-800">{{ $design->gold_quality_label }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Weight</dt>
                        <dd class="font-medium text-slate-800">{{ number_format($design->weight_grams, 2) }} g</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Availability</dt>
                        <dd><span class="jewel-badge-active">Available</span></dd>
                    </div>
                </dl>

                @if($design->description)
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h2 class="text-sm font-semibold text-slate-700 mb-2">Description</h2>
                        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $design->description }}</p>
                    </div>
                @endif

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    @auth
                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('orders.create', ['catalog' => $design->id]) }}" class="jewel-btn flex-1 text-center px-6 py-3">
                                Order This Item
                            </a>
                        @endif
                    @else
                        <a href="{{ route('purchase', $design) }}" class="jewel-btn flex-1 text-center px-6 py-3">
                            Register to Order
                        </a>
                        <a href="{{ route('purchase.login', $design) }}" class="jewel-btn-outline flex-1 text-center px-6 py-3 border-white/25 text-white hover:bg-white/10">
                            Already have an account? Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
