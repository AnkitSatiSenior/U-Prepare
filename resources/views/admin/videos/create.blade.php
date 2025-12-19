<x-app-layout>
    <div class="container">
        <x-admin.breadcrumb-header 
            icon="fas fa-video text-primary" 
            title="Add Video"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['route' => 'admin.videos.index', 'label' => 'Videos'],
                ['label' => 'Add']
            ]"  
        />

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Text</label>
                        <input type="text" name="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" name="img" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="order" value="0" class="form-control">
                    </div>
                    <button class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
