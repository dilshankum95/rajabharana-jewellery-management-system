<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="jewel-page-title text-2xl">Create Account</h2>
        <p class="jewel-page-subtitle mt-2">Register to order custom jewellery — all customer details are required</p>
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
            <label for="phone" class="jewel-label">{{ __('Phone Number') }} *</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                minlength="7" maxlength="25" pattern="[\+]?[0-9\s\-().]{7,25}"
                placeholder="0771234567" class="jewel-input mt-1.5" />
            <x-form-hint>7–15 digits. Spaces, +, and hyphens allowed.</x-form-hint>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <label for="address" class="jewel-label">{{ __('Address') }} *</label>
            <textarea id="address" name="address" rows="2" required minlength="5" maxlength="500"
                placeholder="Street, area, postal code"
                class="jewel-input mt-1.5">{{ old('address') }}</textarea>
            <x-form-hint>Letters, numbers, and common address punctuation only.</x-form-hint>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div>
            <label for="city" class="jewel-label">{{ __('City') }} *</label>
            <input id="city" type="text" name="city" value="{{ old('city') }}" required autocomplete="address-level2"
                minlength="2" maxlength="100" pattern="[A-Za-z\s'\-.]+"
                placeholder="e.g. Colombo" class="jewel-input mt-1.5" />
            <x-form-hint>Letters, spaces, hyphens, and periods only.</x-form-hint>
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="jewel-label">{{ __('Password') }} *</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                minlength="8" maxlength="255"
                placeholder="••••••••" class="jewel-input mt-1.5" />
            <x-password-requirements />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="jewel-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                minlength="8" maxlength="255"
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
