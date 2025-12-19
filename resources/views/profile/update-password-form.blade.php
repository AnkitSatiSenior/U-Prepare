<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        {{-- Current Password --}}
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Current Password') }}" />
            <x-input id="current_password" type="password" class="mt-1 block w-full"
                     wire:model="state.current_password"
                     autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        {{-- New Password + Strength Meter + Checklist --}}
        <div class="col-span-6 sm:col-span-4" x-data="{ password: '' }">
            <x-label for="password" value="{{ __('New Password') }}" />
            <x-input id="password" type="password" class="mt-1 block w-full"
                     x-model="password"
                     wire:model="state.password"
                     autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />

            {{-- Password Strength Meter --}}
            <div class="mt-2">
                <div class="h-2 rounded bg-gray-200 overflow-hidden">
                    <div class="h-2 transition-all duration-300"
                         :class="{
                             'bg-red-500 w-1/4': password.length > 0 && password.length < 8,
                             'bg-yellow-500 w-2/4': password.length >= 8 && (!/[A-Z]/.test(password) || !/[0-9]/.test(password)),
                             'bg-blue-500 w-3/4': password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password),
                             'bg-green-600 w-full': password.length >= 12 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password),
                         }">
                    </div>
                </div>

                {{-- Strength Text --}}
                <span class="text-sm text-gray-600"
                      x-text="password.length < 8 
                          ? 'Weak (min 8 chars required)' 
                          : /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password) 
                              ? 'Strong Password' 
                              : 'Medium Strength'">
                </span>
            </div>

            {{-- Checklist --}}
            <ul class="mt-2 text-sm text-gray-600 space-y-1">
                <li :class="password.length >= 8 ? 'text-green-600' : 'text-gray-500'">
                    ✔ At least 8 characters
                </li>
                <li :class="/[A-Z]/.test(password) ? 'text-green-600' : 'text-gray-500'">
                    ✔ At least one uppercase letter
                </li>
                <li :class="/[0-9]/.test(password) ? 'text-green-600' : 'text-gray-500'">
                    ✔ At least one number
                </li>
                <li :class="/[^A-Za-z0-9]/.test(password) ? 'text-green-600' : 'text-gray-500'">
                    ✔ At least one special character
                </li>
            </ul>
        </div>

        {{-- Confirm Password (NO checklist here) --}}
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full"
                     wire:model="state.password_confirmation"
                     autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button>
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
