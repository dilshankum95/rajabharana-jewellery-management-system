<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} — Sign In</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=lato:400,500,600,700|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-jewel-charcoal antialiased">
        <div class="min-h-screen flex">
            {{-- Brand panel --}}
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-jewel-gradient">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 25%, #C5A059 1px, transparent 1px), radial-gradient(circle at 75% 75%, #C5A059 1px, transparent 1px); background-size: 48px 48px;"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-jewel-gold/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-jewel-burgundy/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10 flex flex-col justify-center px-16 xl:px-24">
                    <div class="flex items-center gap-4 mb-10">
                        <x-jewellery-logo class="w-14 h-14 text-jewel-gold" />
                        <div>
                            <p class="font-display text-3xl font-semibold text-jewel-cream tracking-wide">Rajabharana</p>
                            <p class="text-jewel-gold-light/70 text-sm tracking-[0.15em] uppercase mt-0.5">Jewellery Management System</p>
                        </div>
                    </div>

                    <h1 class="font-display text-4xl xl:text-5xl font-medium text-jewel-cream leading-tight">
                        Crafted with<br>
                        <span class="text-transparent bg-clip-text bg-gold-shimmer">precision & elegance</span>
                    </h1>

                    <p class="mt-6 text-jewel-gold-light/60 text-lg max-w-md leading-relaxed">
                        Manage inventory, sales, and customers from one refined workspace built for fine jewellery businesses.
                    </p>

                    <div class="mt-12 flex items-center gap-6">
                        <div class="h-px w-12 bg-jewel-gold/40"></div>
                        <p class="text-jewel-gold/50 text-xs tracking-[0.3em] uppercase">Est. Excellence</p>
                    </div>
                </div>
            </div>

            {{-- Form panel --}}
            <div class="flex-1 flex flex-col justify-center items-center bg-jewel-cream px-6 py-12 sm:px-10">
                <div class="lg:hidden flex flex-col items-center mb-8">
                    <x-jewellery-logo class="w-12 h-12 text-jewel-gold mb-3" />
                    <p class="font-display text-2xl font-semibold text-jewel-dark">Rajabharana</p>
                    <p class="text-jewel-gold-dark text-xs tracking-[0.15em] uppercase mt-1">Jewellery Management System</p>
                </div>

                <div class="w-full max-w-md">
                    <div class="bg-white rounded-2xl shadow-xl shadow-jewel-dark/5 border border-jewel-gold/10 px-8 py-10 sm:px-10">
                        {{ $slot }}
                    </div>

                    <p class="mt-8 text-center text-xs text-gray-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
