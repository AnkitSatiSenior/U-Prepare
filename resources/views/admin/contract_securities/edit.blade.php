<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-edit text-primary" 
            title="Edit Security"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Contract Securities'],
                ['label' => 'Edit'],
            ]" 
        />

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary"><i class="fas fa-pen me-2"></i> Edit Security</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.contracts.securities.update', [$contract, $security]) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.contract_securities.form', ['contractSecurity' => $security])
    <button type="submit" class="btn btn-primary">Update</button>
</form>

            </div>
        </div>
    </div>
</x-app-layout>
