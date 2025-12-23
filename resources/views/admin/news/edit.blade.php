<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-newspaper text-primary"
            title="News Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.news.index', 'label' => 'News'],
                ['label' => 'Edit']
            ]"
        />

        <!-- Alerts -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" message="Please fix the errors below." dismissible />
                </div>
            </div>
        @endif

        <!-- News Edit Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i> Edit News
                </h5>
                <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Title EN -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                            <input type="text" name="title_en"
                                   class="form-control @error('title_en') is-invalid @enderror"
                                   value="{{ old('title_en', $news->title_en) }}" required>
                            @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Title HI -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (HI) <span class="text-danger">*</span></label>
                            <input type="text" name="title_hi"
                                   class="form-control @error('title_hi') is-invalid @enderror"
                                   value="{{ old('title_hi', $news->title_hi) }}" required>
                            @error('title_hi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Body EN -->
                    <div class="mb-3">
                        <label class="form-label">Body (EN)</label>
                        <textarea name="body_en" rows="6"
                                  class="form-control tinymce-editor @error('body_en') is-invalid @enderror">{{ old('body_en', $news->body_en) }}</textarea>
                        @error('body_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Body HI -->
                    <div class="mb-3">
                        <label class="form-label">Body (HI)</label>
                        <textarea name="body_hi" rows="6"
                                  class="form-control tinymce-editor @error('body_hi') is-invalid @enderror">{{ old('body_hi', $news->body_hi) }}</textarea>
                        @error('body_hi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label class="form-label">File Upload</label>
                        <input type="file" name="file" 
                               class="form-control @error('file') is-invalid @enderror">
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        @if($news->file)
                            <div class="mt-2">
                                <small class="text-muted">Current File:</small><br>
                                <a href="{{ asset('storage/' . $news->file) }}" target="_blank">
                                    <i class="fas fa-file-alt text-primary"></i> View File
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $news->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $news->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Update
                        </button>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '.tinymce-editor',
                promotion: false,
                branding: false,
                menubar: false,
                plugins: 'link lists table code help',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table | code help',
                skin: 'oxide',
                content_css: 'default',
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });
        });
    </script>
</x-app-layout>
