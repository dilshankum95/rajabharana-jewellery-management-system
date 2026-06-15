<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="jewel-page-title text-2xl">Confirm Password</h2>
        <p class="jewel-page-subtitle mt-2">Please confirm your password to continue</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" maxlength="255" class="mt-1.5" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <button type="submit" class="jewel-btn w-full">{{ __('Confirm') }}</button>
    </form>
</x-guest-layout>
