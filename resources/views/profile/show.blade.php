<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-dark">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- Update Profile Information --}}
                @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    <div class="mb-5">
                        <h5 class="text-lg font-medium text-primary m-3">{{ __('Update Profile Information') }}</h5>
                        @livewire('profile.update-profile-information-form')
                    </div>
                    <hr>
                @endif

                {{-- Update Password --}}
                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                    <div class="mb-5">
                        <h5 class="text-lg font-medium text-primary m-3">{{ __('Update Password') }}</h5>
                        @livewire('profile.update-password-form')
                    </div>
                    <hr>
                @endif

                {{-- Two Factor Authentication --}}
                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <div class="mb-5">
                        <h5 class="text-lg font-medium text-primary m-3">{{ __('Two-Factor Authentication') }}</h5>
                        @livewire('profile.two-factor-authentication-form')
                    </div>
                    <hr>
                @endif

                {{-- Logout Other Browser Sessions --}}
                <div class="mb-5">
                    <h5 class="text-lg font-medium text-primary m-3">{{ __('Logout Other Browser Sessions') }}</h5>
                    @livewire('profile.logout-other-browser-sessions-form')
                </div>

              

            </div>
        </div>
    </div>
</x-app-layout>
