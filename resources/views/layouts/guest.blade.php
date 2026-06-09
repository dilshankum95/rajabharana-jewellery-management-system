<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            <div class="hidden lg:flex lg:w-[45%] xl:w-1/2 relative overflow-hidden bg-jewel-hero">
                <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23E9B44C\" fill-opacity=\"0.08\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
                <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-jewel-rose/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-jewel-gold/10 rounded-full blur-3xl translate-y-1/3 -translate-x-1/3"></div>

                <div class="relative z-10 flex flex-col justify-center px-14 xl:px-24 animate-fade-in">
                    <div class="flex items-center gap-4 mb-12">
                        <div class="p-3 rounded-2xl bg-gradient-to-br from-jewel-rose/20 to-jewel-gold/20 ring-1 ring-jewel-gold/30 shadow-glow">
                            <x-jewellery-logo class="w-12 h-12 text-jewel-gold" />
                        </div>
                        <div>
                            <p class="font-display text-3xl font-semibold text-white tracking-wide">Rajabharana</p>
                            <p class="text-slate-400 text-xs tracking-[0.2em] uppercase mt-1">Jewellery Management</p>
                        </div>
                    </div>

                    <h1 class="font-display text-4xl xl:text-5xl font-semibold text-white leading-[1.15]">
                        Where every piece<br>
                        <span class="text-transparent bg-clip-text bg-gold-shimmer">tells a story</span>
                    </h1>

                    <p class="mt-8 text-slate-400 text-lg max-w-md leading-relaxed">
                        Order bespoke jewellery, track your designs, and experience craftsmanship passed down through generations.
                    </p>

                    <div class="mt-14 grid grid-cols-3 gap-4 max-w-md">
                        @foreach([['22K+', 'Gold Quality'], ['100%', 'Handcrafted'], ['Custom', 'Designs']] as [$val, $lbl])
                            <div class="text-center p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <p class="font-display text-xl font-semibold text-jewel-gold">{{ $val }}</p>
                                <p class="text-[10px] uppercase tracking-widest text-slate-500 mt-1">{{ $lbl }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex-1 flex flex-col justify-center items-center jewel-bg-pattern px-6 py-12 sm:px-10">
                <div class="lg:hidden flex flex-col items-center mb-10 animate-slide-up">
                    <div class="p-3 rounded-2xl bg-white shadow-jewel ring-1 ring-jewel-rose/20 mb-4">
                        <x-jewellery-logo class="w-10 h-10 text-jewel-gold" />
                    </div>
                    <p class="font-display text-2xl font-semibold text-slate-800">Rajabharana</p>
                    <p class="text-slate-500 text-xs tracking-[0.15em] uppercase mt-1">Jewellery Management</p>
                </div>

                <div class="w-full max-w-md animate-slide-up">
                    <div class="jewel-auth-card">{{ $slot }}</div>
                    <p class="mt-8 text-center text-xs text-slate-400">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                </div>
            </div>
        </div>
    </body>
</html>
