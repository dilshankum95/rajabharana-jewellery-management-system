@if(auth()->user()->isTechnician())
    <x-technician-layout>
        <x-slot name="header">
            <div>
                <h1 class="jewel-page-title text-xl">{{ __('Profile Settings') }}</h1>
                <p class="jewel-page-subtitle">Manage your account information and security</p>
            </div>
        </x-slot>

        <div class="max-w-3xl space-y-6">
            <div class="jewel-card jewel-card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="jewel-card jewel-card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </x-technician-layout>
@elseif(auth()->user()->isAdminOrStaff())
    <x-admin-layout>
        <x-slot name="header">
            <div>
                <h1 class="jewel-page-title text-xl">{{ __('Profile Settings') }}</h1>
                <p class="jewel-page-subtitle">Manage your account information and security</p>
            </div>
        </x-slot>

        <div class="max-w-3xl space-y-6">
            <div class="jewel-card jewel-card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="jewel-card jewel-card-body">
                @include('profile.partials.update-password-form')
            </div>
            @if(auth()->user()->isCustomer())
                <div class="jewel-card jewel-card-body border-red-100">
                    @include('profile.partials.delete-user-form')
                </div>
            @endif
        </div>
    </x-admin-layout>
@else
    <x-app-layout>
        <x-slot name="header">
            <div>
                <h1 class="jewel-page-title">{{ __('Profile Settings') }}</h1>
                <p class="jewel-page-subtitle">Manage your account information and security</p>
            </div>
        </x-slot>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="jewel-card jewel-card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
                <div class="jewel-card jewel-card-body">
                    @include('profile.partials.update-password-form')
                </div>
                <div class="jewel-card jewel-card-body border-red-100">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </x-app-layout>
@endif
