<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-plus text-primary" title="Create EAP Project" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => 'Home'],
            ['route' => 'admin.project.index', 'label' => 'Projects'],
            ['label' => 'Create'],
        ]" />

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.project.store') }}" method="POST">
            @csrf
            @include('admin.project._form', ['project' => $project])

            <div class="mb-5 d-flex justify-content-end">
                <a href="{{ route('admin.project.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-5">Save Project Information</button>
            </div>
        </form>
    </div>
</x-app-layout>
