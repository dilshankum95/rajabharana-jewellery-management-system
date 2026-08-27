@if($design && $design->images->isNotEmpty())
    <div class="jewel-card jewel-card-body mt-6">
        <h3 class="jewel-section-title mb-4">Manage Images ({{ $design->images->count() }})</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($design->images as $image)
                <div class="relative group rounded-xl overflow-hidden border border-stone-200 bg-stone-50">
                    <img src="{{ $image->url }}" alt="" class="w-full h-32 object-cover">
                    @if($image->is_primary)
                        <span class="absolute top-2 left-2 text-[10px] uppercase tracking-wider bg-jewel-gold/90 text-white px-2 py-0.5 rounded-full">Primary</span>
                    @endif
                    <div class="p-2 flex gap-1">
                        @if(!$image->is_primary)
                            <form method="POST" action="{{ route('admin.catalog.images.primary', [$design, $image]) }}" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full text-xs py-1.5 rounded-lg border border-stone-200 text-stone-600 hover:bg-stone-100 transition">Set Primary</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.catalog.images.destroy', [$design, $image]) }}"
                            onsubmit="return confirm('Remove this image?')" class="{{ $image->is_primary ? 'flex-1' : '' }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full text-xs py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
