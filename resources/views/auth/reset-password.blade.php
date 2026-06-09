<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="jewel-page-title text-2xl">Reset Password</h2>
        <p class="jewel-page-subtitle mt-2">Choose a new password for your account</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" class="mt-1.5" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" class="mt-1.5" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1.5" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="jewel-btn w-full">{{ __('Reset Password') }}</button>
    </form>
</x-guest-layout>
