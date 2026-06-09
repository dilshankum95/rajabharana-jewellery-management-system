<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="jewel-page-title text-2xl">Forgot Password</h2>
        <p class="jewel-page-subtitle mt-2">Enter your email and we'll send you a reset link</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="jewel-label">{{ __('Email Address') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com" class="jewel-input mt-1.5" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <button type="submit" class="jewel-btn w-full">{{ __('Send Reset Link') }}</button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="jewel-link no-underline hover:underline">&larr; Back to sign in</a>
    </p>
</x-guest-layout>
