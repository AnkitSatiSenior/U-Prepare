<x-app-layout>
    <div class="container-fluid">
        <!-- Header + Breadcrumb -->
        <x-admin.breadcrumb-header
    icon="fas fa-id-card-alt text-primary"
    :title="isset($designation) ? 'Edit Designation' : 'Create New Designation'"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['route' => 'admin.designations.index', 'label' => 'Designations'],
        ['label' => isset($designation) ? 'Edit' : 'Create']
    ]"
/>


        <!-- Success/Error Messages -->
        <div class="row mb-3">
            <div class="col-md-12">
                @if (session('success'))
                    <x-alert type="success" :message="session('success')" dismissible />
                @endif

                @if ($errors->any())
                    <x-alert type="danger" dismissible>
                        <strong>Whoops!</strong> There were some problems with your input.
                        <ul class="mt-2 mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif
            </div>
        </div>
        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas {{ isset($designation) ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                    {{ isset($designation) ? 'Edit Designation Details' : 'Add New Designation' }}
                </h5>
                <a href="{{ route('admin.designations.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Designations
                </a>
            </div>

            <div class="card-body">
                <form
                    action="{{ isset($designation) ? route('admin.designations.update', $designation) : route('admin.designations.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($designation))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <!-- Designation Title -->
                        <div class="col-md-6">
                            <x-label for="title" value="Designation Title" required />
                            <x-input id="title" name="title" type="text" :value="old('title', $designation->title ?? '')"
                                placeholder="Enter designation title" required autofocus />
                            <x-input-error for="title" class="mt-2" />
                        </div>


                    </div>

                    <!-- Form Actions -->
                    <div class="mt-4 d-flex justify-content-end border-top pt-3">
                        <x-button type="button" variant="secondary"
                            onclick="window.location.href='{{ route('admin.designations.index') }}'" class="me-2">
                            <i class="fas fa-times me-1"></i> Cancel
                        </x-button>

                        <x-button type="submit" variant="primary">
                            <i class="fas {{ isset($designation) ? 'fa-save' : 'fa-plus-circle' }} me-1"></i>
                            {{ isset($designation) ? 'Update Designation' : 'Create Designation' }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
