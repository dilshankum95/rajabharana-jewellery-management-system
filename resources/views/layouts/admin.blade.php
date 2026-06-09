<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex jewel-bg-pattern" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-jewel-dark/60 backdrop-blur-sm lg:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-jewel-gradient transform transition-transform duration-300 lg:translate-x-0 lg:static flex flex-col shadow-2xl">
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #BFAF96 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="relative flex items-center gap-3 px-6 h-16 border-b border-white/10">
                <div class="p-2 rounded-xl bg-gradient-to-br from-jewel-rose/20 to-jewel-gold/20 ring-1 ring-jewel-gold/30">
                    <x-jewellery-logo class="w-7 h-7 text-jewel-gold" />
                </div>
                <div>
                    <p class="font-display text-sm font-semibold text-white">Rajabharana</p>
                    <p class="text-[10px] uppercase tracking-widest text-jewel-gold/70">Admin Panel</p>
                </div>
            </div>

            <nav class="relative flex-1 px-3 py-6 space-y-1">
                @foreach([
                    ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'admin.orders.index', 'pattern' => 'admin.orders.*', 'label' => 'Orders', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['route' => 'admin.customers.index', 'pattern' => 'admin.customers.*', 'label' => 'Customers', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'admin.catalog.index', 'pattern' => 'admin.catalog.*', 'label' => 'Catalog', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ] as $item)
                    <a href="{{ route($item['route']) }}"
                        class="jewel-sidebar-link {{ request()->routeIs($item['pattern']) ? 'jewel-sidebar-link-active' : 'jewel-sidebar-link-inactive' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="relative px-3 py-4 border-t border-jewel-gold/20">
                <div class="flex items-center gap-3 px-3 py-2 mb-2">
                    <span class="w-9 h-9 rounded-full bg-gradient-to-br from-jewel-gold to-jewel-gold-dark flex items-center justify-center text-xs font-bold text-jewel-dark">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-jewel-cream truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-jewel-gold/50 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="jewel-sidebar-link jewel-sidebar-link-inactive">Profile Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="jewel-sidebar-link jewel-sidebar-link-inactive w-full">Sign Out</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 shadow-sm h-16 flex items-center px-4 sm:px-6 lg:px-8 gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl text-jewel-charcoal hover:bg-jewel-cream transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                @isset($header)<div class="flex-1 animate-slide-up">{{ $header }}</div>@endisset
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 animate-fade-in">
                @if(session('success'))<x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>@endif
                @if(session('error'))<x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>@endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
