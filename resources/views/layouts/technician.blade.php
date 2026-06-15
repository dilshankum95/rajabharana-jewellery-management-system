<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Workshop' }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex jewel-bg-pattern" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-jewel-dark/60 backdrop-blur-sm lg:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-800 to-slate-900 transform transition-transform duration-300 lg:translate-x-0 lg:static flex flex-col shadow-2xl">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle, #BFAF96 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="relative flex items-center gap-3 px-6 h-16 border-b border-white/10">
                <div class="p-2 rounded-xl bg-violet-500/20 ring-1 ring-violet-400/30">
                    <x-jewellery-logo class="w-7 h-7 text-violet-300" />
                </div>
                <div>
                    <p class="font-display text-sm font-semibold text-white">Rajabharana</p>
                    <p class="text-[10px] uppercase tracking-widest text-violet-300/70">Workshop Panel</p>
                </div>
            </div>

            <nav class="relative flex-1 px-3 py-6 space-y-1">
                <a href="{{ route('technician.dashboard') }}"
                    class="jewel-sidebar-link {{ request()->routeIs('technician.dashboard') ? 'jewel-sidebar-link-active' : 'jewel-sidebar-link-inactive' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    My Jobs
                </a>
            </nav>

            <div class="relative px-3 py-4 border-t border-white/10">
                <x-live-datetime variant="dark" class="px-3 mb-3 text-violet-300/60" />
                <div class="flex items-center gap-3 px-3 py-2 mb-2">
                    <x-user-avatar :user="Auth::user()" size="md" />
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-violet-300/50 truncate">{{ Auth::user()->role->label() }}</p>
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
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
