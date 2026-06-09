@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col bg-jewel-dark">
    <div class="fixed inset-0 opacity-30 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23E9B44C\" fill-opacity=\"0.06\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] pointer-events-none"></div>
    <div class="fixed top-0 right-0 w-[600px] h-[600px] bg-jewel-rose/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>

    @include('layouts.partials.public-nav')

    <main class="relative z-10 flex-1">
        {{ $slot }}
    </main>

    <footer class="relative z-10 text-center py-8 text-xs text-slate-500 border-t border-white/5">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</body>
</html>
