<nav class="relative z-10 flex items-center justify-between px-6 py-5 sm:px-10 lg:px-16 border-b border-white/5">
    <div class="flex items-center gap-6">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="p-2 rounded-xl bg-gradient-to-br from-jewel-rose/20 to-jewel-gold/20 ring-1 ring-jewel-gold/30 group-hover:shadow-glow transition">
                <x-jewellery-logo class="w-8 h-8 text-jewel-gold" />
            </div>
            <span class="font-display text-xl font-semibold text-white">Rajabharana</span>
        </a>
        <a href="{{ route('catalog.index') }}"
            class="hidden sm:inline text-sm font-semibold {{ request()->routeIs('catalog.*') ? 'text-jewel-gold' : 'text-slate-300 hover:text-white' }} transition">
            Our Catalog
        </a>
    </div>
    <div class="flex items-center gap-3">
        @auth
            @if(auth()->user()->isCustomer())
                <a href="{{ route('dashboard') }}" class="jewel-btn text-xs px-5 py-2">My Account</a>
            @else
                <a href="{{ route('admin.dashboard') }}" class="jewel-btn text-xs px-5 py-2">Admin Panel</a>
            @endif
        @else
            <a href="{{ route('register') }}" class="jewel-btn text-xs px-5 py-2">Start Ordering</a>
        @endauth
    </div>
</nav>
