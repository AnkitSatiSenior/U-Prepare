<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information, timeline, and professional experience.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-3">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="username" value="{{ __('Username') }}" />
            <x-input id="username" type="text" class="mt-1 block w-full" wire:model="state.username" required autocomplete="username" />
            <x-input-error for="username" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required />
            <x-input-error for="email" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="phone_no" value="{{ __('Phone Number') }}" />
            <x-input id="phone_no" type="tel" class="mt-1 block w-full" wire:model="state.phone_no" />
            <x-input-error for="phone_no" class="mt-2" />
        </div>

        <div class="col-span-6 border-b border-gray-200 mt-4 mb-2 pb-2">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Timeline & Experience') }}</h3>
        </div>

        <div class="col-span-6 sm:col-span-2">
            <x-label for="dob" value="{{ __('Date of Birth') }}" />
            <x-input id="dob" type="date" class="mt-1 block w-full" wire:model="state.dob" />
            <x-input-error for="dob" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-2">
            <x-label for="date_of_joining" value="{{ __('Date of Joining') }}" />
            <x-input id="date_of_joining" type="date" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" wire:model="state.date_of_joining" />
        </div>

        <div class="col-span-6 sm:col-span-2">
            <x-label for="total_work_experience" value="{{ __('Total Work Experience') }}" />
            <x-input id="total_work_experience" type="text" class="mt-1 block w-full" wire:model="state.total_work_experience" placeholder="e.g., 10+ years" />
            <x-input-error for="total_work_experience" class="mt-2" />
        </div>

        <div class="col-span-6 border-b border-gray-200 mt-4 mb-2 pb-2">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Professional Details') }}</h3>
        </div>

        <div class="col-span-6">
            <x-label for="qualification" value="{{ __('Qualification(s)') }}" />
            <textarea id="qualification" wire:model="state.qualification" rows="2" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" placeholder="B.Tech in Electronic & Communication..."></textarea>
            <x-input-error for="qualification" class="mt-2" />
        </div>

        <div class="col-span-6">
            <x-label for="area_of_expertise" value="{{ __('Area of Expertise') }}" />
            <textarea id="area_of_expertise" wire:model="state.area_of_expertise" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" placeholder="Hardware Networking, Cloud Computing..."></textarea>
            <x-input-error for="area_of_expertise" class="mt-2" />
        </div>

        <div class="col-span-6">
            <x-label for="research_publication_citation" value="{{ __('Research / Publications / Citations') }}" />
            <x-input id="research_publication_citation" type="text" class="mt-1 block w-full" wire:model="state.research_publication_citation" placeholder="Leave blank or write 'No' if none" />
            <x-input-error for="research_publication_citation" class="mt-2" />
        </div>

        <div class="col-span-6">
            <x-label for="previous_experience" value="{{ __('Previous Experience (Last 3 Organizations)') }}" />
            <textarea id="previous_experience" wire:model="state.previous_experience" rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" placeholder="Details of previous employment..."></textarea>
            <x-input-error for="previous_experience" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled">
            {{ __('Save Profile Changes') }}
        </x-button>
    </x-slot>
</x-form-section>
