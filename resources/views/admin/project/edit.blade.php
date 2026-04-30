<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-edit text-primary" title="Edit EAP Project" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => 'Home'],
            ['route' => 'admin.project.index', 'label' => 'Projects'],
            ['label' => 'Edit Project'],
        ]" />
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form action="{{ route('admin.project.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.project._form', ['project' => $project])

            <div class="mb-5 d-flex justify-content-between">
                <a href="{{ route('admin.project.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
                <button type="submit" class="btn btn-primary px-5 shadow">
                    <i class="fas fa-save me-1"></i> Update Project Information
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
