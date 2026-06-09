<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="jewel-page-title text-2xl">Create Account</h2>
        <p class="jewel-page-subtitle mt-2">Register to order custom jewellery from Rajabharana</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="jewel-label">{{ __('Full Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                minlength="2" maxlength="255"
                placeholder="Your full name" class="jewel-input mt-1.5" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="jewel-label">{{ __('Email Address') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                maxlength="255"
                placeholder="you@example.com" class="jewel-input mt-1.5" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="phone" class="jewel-label">{{ __('Phone Number') }}</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                minlength="7" maxlength="25" pattern="[\+]?[0-9\s\-().]{7,25}"
                placeholder="0771234567" class="jewel-input mt-1.5" />
            <p class="mt-1 text-xs text-slate-400">7–15 digits. Spaces, +, and hyphens allowed.</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="jewel-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="••••••••" class="jewel-input mt-1.5" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="jewel-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="••••••••" class="jewel-input mt-1.5" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="jewel-btn w-full">
                {{ __('Create Account') }}
            </button>
        </div>
    </form>

    <div class="mt-8">
        <div class="jewel-divider">or</div>
        <p class="mt-6 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-jewel-gold-dark hover:text-jewel-gold transition">
                {{ __('Sign in') }}
            </a>
        </p>
    </div>
</x-guest-layout>
