<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-edit text-primary"
            title="Edit Slide"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.slides.index', 'label' => 'Slides'],
                ['label' => 'Edit']
            ]"
        />

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.slides.update', $slide) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Show Current Image -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Image</label><br>
                            @if($slide->img)
                                <img src="{{ asset('storage/app/public/' . $slide->img) }}" 
                                     alt="Slide Image" 
                                     class="img-thumbnail mb-2" 
                                     width="200">
                            @else
                                <p class="text-muted">No image uploaded</p>
                            @endif

                            <label class="form-label">Change Image</label>
                            <input type="file" name="img" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty if you don’t want to change</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head</label>
                            <input type="text" name="head" value="{{ $slide->head }}" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Subh</label>
                            <textarea name="subh" class="form-control" rows="3">{{ $slide->subh }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="btn_text" value="{{ $slide->btn_text }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link</label>
                            <input type="url" name="link" value="{{ $slide->link }}" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" name="order" value="{{ $slide->order }}" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ $slide->status ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$slide->status ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                    <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
