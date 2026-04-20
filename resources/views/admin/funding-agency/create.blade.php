<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-plus" title="Add Funding Agency" :breadcrumbs="[
            ['route' => 'admin.funding-agency.index', 'label' => 'Agencies'],
            ['label' => 'Create']
        ]" />

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('admin.funding-agency.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Agency Name *</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. World Bank">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Agency Code</label>
                                <input type="text" name="code" class="form-control" placeholder="e.g. WB">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Agency</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>