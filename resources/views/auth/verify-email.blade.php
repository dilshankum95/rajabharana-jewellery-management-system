<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="jewel-page-title text-2xl">Verify Email</h2>
        <p class="jewel-page-subtitle mt-2">Please verify your email address to continue</p>
    </div>

    <p class="text-sm text-gray-500 text-center mb-6">
        {{ __('Thanks for signing up! Click the link in your email to verify your account.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" class="mb-6">{{ __('A new verification link has been sent to your email.') }}</x-alert>
    @endif

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="jewel-btn w-full sm:w-auto">{{ __('Resend Email') }}</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm jewel-link">{{ __('Log Out') }}</button>
        </form>
    </div>
</x-guest-layout>
