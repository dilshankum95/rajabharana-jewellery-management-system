<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-jewel-dark overflow-hidden">
        <div class="absolute inset-0 opacity-30 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23E9B44C\" fill-opacity=\"0.06\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-jewel-rose/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-jewel-gold/15 rounded-full blur-3xl translate-y-1/2 -translate-x-1/3"></div>

        <nav class="relative z-10 flex items-center justify-between px-6 py-5 sm:px-10 lg:px-16">
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-gradient-to-br from-jewel-rose/20 to-jewel-gold/20 ring-1 ring-jewel-gold/30">
                        <x-jewellery-logo class="w-8 h-8 text-jewel-gold" />
                    </div>
                    <span class="font-display text-xl font-semibold text-white">Rajabharana</span>
                </a>
                <a href="{{ route('catalog.index') }}" class="hidden sm:inline text-sm font-semibold text-slate-300 hover:text-white transition">Our Catalog</a>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="jewel-btn text-xs px-5 py-2">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-white px-4 py-2 transition">Sign In</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="jewel-btn text-xs px-5 py-2">Register</a>
                    @endif
                @endauth
            </div>
        </nav>

        <main class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12 px-6 py-16 sm:px-10 lg:px-16 lg:py-24 max-w-7xl mx-auto">
            <div class="flex-1 text-center lg:text-left animate-slide-up">
                <span class="inline-block px-4 py-1.5 rounded-full bg-jewel-rose/20 text-jewel-rose-light text-xs font-semibold uppercase tracking-widest mb-6 border border-jewel-rose/30">Fine Jewellery · Custom Orders</span>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-semibold text-white leading-tight">
                    Crafted with<br>
                    <span class="text-transparent bg-clip-text bg-gold-shimmer">timeless elegance</span>
                </h1>
                <p class="mt-6 text-slate-400 text-lg max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Order bespoke gold jewellery from our catalog or submit your own design. Track every step from workshop to delivery.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('catalog.index') }}" class="jewel-btn px-8 py-3">Browse Catalog</a>
                    @guest
                        <a href="{{ route('register') }}" class="jewel-btn px-8 py-3">Start Ordering</a>
                    @else
                        <a href="{{ route('orders.create') }}" class="jewel-btn px-8 py-3">Place an Order</a>
                        <a href="{{ route('dashboard') }}" class="jewel-btn-outline border-white/20 text-white hover:text-jewel-rose-dark px-8 py-3">My Dashboard</a>
                    @endguest
                </div>
            </div>

            <div class="flex-1 grid grid-cols-2 gap-4 max-w-md lg:max-w-lg animate-fade-in">
                <div class="col-span-2 jewel-card p-6 bg-white/5 border-white/10 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <x-jewellery-logo class="w-12 h-12 text-jewel-gold shrink-0" />
                        <div>
                            <p class="font-display text-lg font-semibold text-white">Custom Designs</p>
                            <p class="text-sm text-slate-400 mt-1">Upload your reference and we'll craft it in gold</p>
                        </div>
                    </div>
                </div>
                @foreach([['22K', 'Pure Gold'], ['Track', 'Your Order']] as [$val, $lbl])
                    <div class="jewel-card p-5 bg-white/5 border-white/10 backdrop-blur-md text-center">
                        <p class="font-display text-3xl font-semibold text-jewel-gold">{{ $val }}</p>
                        <p class="text-[10px] uppercase tracking-widest text-slate-500 mt-2">{{ $lbl }}</p>
                    </div>
                @endforeach
            </div>
        </main>

        <footer class="relative z-10 text-center py-8 text-xs text-slate-500 border-t border-white/5">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </footer>
    </div>
</body>
</html>
