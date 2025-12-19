<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-plus-circle text-primary"
            title="Add Slide"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.slides.index', 'label' => 'Slides'],
                ['label' => 'Create']
            ]"
        />

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Image Upload -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slide Image</label>
                            <input type="file" name="img" class="form-control" accept="image/*" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head</label>
                            <input type="text" name="head" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Subh</label>
                            <textarea name="subh" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="btn_text" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link</label>
                            <input type="url" name="link" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" name="order" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                    <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
