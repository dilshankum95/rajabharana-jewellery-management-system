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
                    ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard', 'permission' => 'dashboard.view', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'admin.orders.index', 'pattern' => 'admin.orders.*', 'label' => 'Orders', 'permission' => 'orders.view', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['route' => 'admin.workshop.index', 'pattern' => 'admin.workshop.*', 'label' => 'Workshop', 'permission' => 'production.view', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                    ['route' => 'admin.customers.index', 'pattern' => 'admin.customers.*', 'label' => 'Customers', 'permission' => 'customers.view', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'admin.invoices.index', 'pattern' => 'admin.invoices.*|admin.orders.invoice.*', 'label' => 'Billing', 'permission' => 'billing.view', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'admin.catalog.index', 'pattern' => 'admin.catalog.*', 'label' => 'Inventory', 'permission' => 'catalog.view', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'admin.metal-prices.edit', 'pattern' => 'admin.metal-prices.*', 'label' => 'Metal Prices', 'permission' => 'metal-prices.manage', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Staff Accounts', 'permission' => 'users.manage', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ] as $item)
                    @can('permission', $item['permission'])
                        <a href="{{ route($item['route']) }}"
                            class="jewel-sidebar-link {{ request()->routeIs($item['pattern']) ? 'jewel-sidebar-link-active' : 'jewel-sidebar-link-inactive' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                        </a>
                    @endcan
                @endforeach
            </nav>

            <div class="relative px-3 py-4 border-t border-jewel-gold/20">
                <x-live-datetime variant="dark" class="px-3 mb-3 text-jewel-gold/60" />
                <div class="flex items-center gap-3 px-3 py-2 mb-2">
                    <x-user-avatar :user="Auth::user()" size="md" />
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-jewel-cream truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-jewel-gold/50 truncate">{{ Auth::user()->role->label() }}</p>
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
                <x-live-datetime variant="admin" />
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 animate-fade-in">
                @if(session('success'))<x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>@endif
                @if(session('warning'))<x-alert type="warning" class="mb-6">{{ session('warning') }}</x-alert>@endif
                @if(session('error'))<x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>@endif
                @if(! request()->routeIs('admin.dashboard') && (($deliveryOverdueCount ?? 0) > 0 || ($deliveryDueSoonCount ?? 0) > 0))
                    <x-alert type="warning" class="mb-6">
                        <p class="font-semibold">Delivery reminders active</p>
                        <p class="mt-1">
                            @if(($deliveryOverdueCount ?? 0) > 0)
                                <span class="font-medium">{{ $deliveryOverdueCount }} overdue</span>
                                @if(($deliveryDueSoonCount ?? 0) > 0) · @endif
                            @endif
                            @if(($deliveryDueSoonCount ?? 0) > 0)
                                <span class="font-medium">{{ $deliveryDueSoonCount }} due within {{ config('jewellery.delivery_reminder_days') }} days</span>
                            @endif
                            — review orders and update delivery dates if needed.
                            <a href="{{ route('admin.dashboard') }}" class="underline font-medium ml-1">View dashboard</a>
                        </p>
                    </x-alert>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
