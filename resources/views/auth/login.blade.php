<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="jewel-page-title text-2xl">Welcome Back</h2>
        <p class="jewel-page-subtitle mt-2">Sign in to Rajabharana Jewellery Management System</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="jewel-label">{{ __('Email Address') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                maxlength="255"
                placeholder="you@example.com"
                class="jewel-input mt-1.5"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="jewel-label">{{ __('Password') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                maxlength="255"
                autocomplete="current-password"
                placeholder="••••••••"
                class="jewel-input mt-1.5"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-jewel-gold/40 text-jewel-gold shadow-sm focus:ring-jewel-gold/30"
                />
                <span class="ms-2 text-sm text-gray-500">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="jewel-link">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="jewel-btn w-full">
                {{ __('Sign In') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="mt-8">
            <div class="jewel-divider">or</div>
            <p class="mt-6 text-center text-sm text-gray-500">
                New to the platform?
                <a href="{{ route('register') }}" class="jewel-link no-underline hover:underline">
                    {{ __('Create an account') }}
                </a>
            </p>
        </div>
    @endif
</x-guest-layout>
