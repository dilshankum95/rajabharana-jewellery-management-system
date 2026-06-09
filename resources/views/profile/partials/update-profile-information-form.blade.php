<section>
    <header class="mb-6">
        <h2 class="jewel-section-title">{{ __('Profile Information') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1.5" :value="old('name', $user->name)" required autofocus autocomplete="name" minlength="2" maxlength="255" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1.5" :value="old('email', $user->email)" required autocomplete="username" maxlength="255" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-gray-600">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="jewel-link">{{ __('Re-send verification email') }}</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <x-alert type="success" class="mt-3">{{ __('A new verification link has been sent.') }}</x-alert>
                @endif
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1.5" :value="old('phone', $user->phone)" required autocomplete="tel" minlength="7" maxlength="25" pattern="[\+]?[0-9\s\-().]{7,25}" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <textarea id="address" name="address" rows="2" maxlength="500" minlength="5" class="jewel-input mt-1.5" placeholder="Street, city, postal code">{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div>
            <x-input-label for="city" :value="__('City')" />
            <x-text-input id="city" name="city" type="text" class="mt-1.5" :value="old('city', $user->city)" autocomplete="address-level2" minlength="2" maxlength="100" />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm text-emerald-600 font-medium">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
