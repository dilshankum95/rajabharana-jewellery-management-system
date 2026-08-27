<section>
    <header class="mb-6">
        <h2 class="jewel-section-title">{{ __('Profile Information') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __("Update your account's profile information. Fields marked * are required.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('patch')

        <div class="flex flex-col sm:flex-row sm:items-center gap-5 pb-5 border-b border-jewel-gold/10">
            <x-user-avatar :user="$user" size="lg" id="profile-avatar-preview" />
            <div class="flex-1 space-y-3">
                <div>
                    <x-input-label for="profile_photo" :value="__('Profile Photo')" />
                    <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp"
                        class="mt-1.5 jewel-file-input"
                        onchange="previewProfilePhoto(this)">
                    <p class="mt-1 text-xs text-gray-400">JPG, PNG or WebP. Max 2MB.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                </div>
                @if($user->profile_photo_path)
                    <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remove_profile_photo" value="1" class="rounded border-jewel-gold/40 text-jewel-gold">
                        {{ __('Remove current photo') }}
                    </label>
                @endif
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <span class="text-red-500 text-xs">*</span>
            <x-text-input id="name" name="name" type="text" class="mt-1.5" :value="old('name', $user->name)" required autofocus autocomplete="name" minlength="2" maxlength="255" />
            <x-form-hint>Letters, spaces, hyphens, apostrophes, and periods only.</x-form-hint>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <span class="text-red-500 text-xs">*</span>
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
            <x-input-label for="phone" :value="__('Phone Number *')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1.5" :value="old('phone', $user->phone)" required autocomplete="tel" minlength="7" maxlength="25" pattern="[\+]?[0-9\s\-().]{7,25}" />
            <x-form-hint>7–15 digits. Spaces, +, and hyphens allowed.</x-form-hint>
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Address *')" />
            <textarea id="address" name="address" rows="2" maxlength="500" minlength="5" required class="jewel-input mt-1.5" placeholder="Street, area, postal code">{{ old('address', $user->address) }}</textarea>
            <x-form-hint>Letters, numbers, and common address punctuation only.</x-form-hint>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div>
            <x-input-label for="city" :value="__('City *')" />
            <x-text-input id="city" name="city" type="text" class="mt-1.5" :value="old('city', $user->city)" required autocomplete="address-level2" minlength="2" maxlength="100" />
            <x-form-hint>Letters, spaces, hyphens, and periods only.</x-form-hint>
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm text-emerald-600 font-medium">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        function previewProfilePhoto(input) {
            const preview = document.getElementById('profile-avatar-preview');
            if (!preview || !input.files?.[0]) return;

            const url = URL.createObjectURL(input.files[0]);
            const wrapperClasses = 'w-16 h-16 rounded-full overflow-hidden ring-2 ring-jewel-gold/30 shrink-0';
            const existingImg = preview.tagName === 'IMG'
                ? preview
                : preview.querySelector('img');

            if (existingImg) {
                existingImg.src = url;
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.id = 'profile-avatar-preview';
            wrapper.className = wrapperClasses;

            const img = document.createElement('img');
            img.className = 'h-full w-full object-cover';
            img.alt = @json($user->name);
            img.src = url;

            wrapper.appendChild(img);
            preview.replaceWith(wrapper);
        }
    </script>
</section>
