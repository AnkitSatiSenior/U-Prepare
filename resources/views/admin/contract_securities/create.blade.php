<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-plus-circle text-primary" 
            title="Add New Security"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Contract Securities'],
                ['label' => 'Create'],
            ]" 
        />

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary"><i class="fas fa-file-shield me-2"></i> Create Security</h5>
            </div>
            <div class="card-body">
                {{-- Display global errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.contracts.securities.store', $contract) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.contract_securities.form', ['contractSecurity' => null])
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
