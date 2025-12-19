<x-app-layout>
    <div class="container-fluid py-4">
        <x-admin.breadcrumb-header
            icon="fas fa-tasks text-primary"
            title="{{ isset($alreadyDefinedWorkProgress) ? 'Edit Component' : 'Add Component' }}"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.already_defined_work_progress.index', 'label' => 'Work Components'],
                ['label' => isset($alreadyDefinedWorkProgress) ? 'Edit' : 'Add']
            ]"
        />

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="fas {{ isset($alreadyDefinedWorkProgress) ? 'fa-edit' : 'fa-plus' }} me-2"></i>
                    {{ isset($alreadyDefinedWorkProgress) ? 'Edit Component' : 'Add New Component' }}
                </h5>
                <a href="{{ route('admin.already_defined_work_progress.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ isset($alreadyDefinedWorkProgress) ? route('admin.already_defined_work_progress.update', $alreadyDefinedWorkProgress) : route('admin.already_defined_work_progress.store') }}" method="POST">
                    @csrf
                    @if(isset($alreadyDefinedWorkProgress)) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <x-label for="work_service_id" value="Work Service" required />
                            <x-bootstrap.dropdown 
                                id="work_service_id" 
                                name="work_service_id" 
                                :items="$workServices->map(fn($s) => ['value'=>$s->id,'label'=>$s->name])->toArray()" 
                                :selected="old('work_service_id', $alreadyDefinedWorkProgress->work_service_id ?? '')" />
                            <x-input-error for="work_service_id" />
                        </div>

                        <div class="col-md-4">
                            <x-label for="work_component" value="Work Component" required />
                            <x-input id="work_component" name="work_component" value="{{ old('work_component', $alreadyDefinedWorkProgress->work_component ?? '') }}" />
                            <x-input-error for="work_component" />
                        </div>

                        <div class="col-md-4">
                            <x-label for="type_details" value="Type / Details" />
                            <x-input id="type_details" name="type_details" value="{{ old('type_details', $alreadyDefinedWorkProgress->type_details ?? '') }}" />
                            <x-input-error for="type_details" />
                        </div>

                        <div class="col-md-4">
                            <x-label for="side_location" value="Side / Location" />
                            <x-input id="side_location" name="side_location" value="{{ old('side_location', $alreadyDefinedWorkProgress->side_location ?? '') }}" />
                            <x-input-error for="side_location" />
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas {{ isset($alreadyDefinedWorkProgress) ? 'fa-save' : 'fa-plus' }} me-2"></i>
                            {{ isset($alreadyDefinedWorkProgress) ? 'Update Component' : 'Add Component' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
