<nav x-data="{ open: false }" class="sticky top-0 z-40 jewel-nav-glass">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="p-1.5 rounded-xl bg-gradient-to-br from-jewel-rose/20 to-jewel-gold/20 ring-1 ring-jewel-gold/30 group-hover:shadow-glow transition">
                        <x-jewellery-logo class="w-7 h-7 text-jewel-gold" />
                    </div>
                    <span class="hidden sm:block font-display text-lg font-semibold bg-gold-shimmer bg-clip-text text-transparent">Rajabharana</span>
                </a>

                <div class="hidden sm:flex sm:ms-10 sm:space-x-1">
                    <x-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.*')">{{ __('Catalog') }}</x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-nav-link>
                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index', 'orders.show')">{{ __('My Orders') }}</x-nav-link>
                    <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">{{ __('Notifications') }}</x-nav-link>
                    <x-nav-link :href="route('orders.create')" :active="request()->routeIs('orders.create')">{{ __('Place Order') }}</x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-4">
                <x-live-datetime variant="dark" class="hidden lg:block" />
                @if(($unreadNotificationCount ?? 0) > 0)
                    <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition" title="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-[10px] font-bold text-white flex items-center justify-center">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                    </a>
                @else
                    <a href="{{ route('notifications.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition" title="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </a>
                @endif
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition">
                            <x-user-avatar :user="Auth::user()" size="sm" class="shadow-sm" />
                            <span class="hidden md:block">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <x-user-avatar :user="Auth::user()" size="sm" />
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                            <x-live-datetime class="mt-2" />
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = !open" class="sm:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path :class="{'hidden': !open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div :class="open ? 'block' : 'hidden'" class="sm:hidden bg-jewel-navy border-t border-white/10">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.*')">{{ __('Catalog') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">{{ __('My Orders') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">{{ __('Notifications') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.create')" :active="request()->routeIs('orders.create')">{{ __('Place Order') }}</x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-3 border-t border-white/10 px-4">
            <x-live-datetime variant="dark" class="mb-3" />
            <div class="flex items-center gap-3 mb-2">
                <x-user-avatar :user="Auth::user()" size="sm" />
                <div>
                    <p class="font-semibold text-white">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-slate-400">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
