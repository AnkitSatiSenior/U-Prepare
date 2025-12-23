{{-- resources/views/admin/pages/edit.blade.php --}}
<x-app-layout>
    <div class="container py-4">

        {{-- Breadcrumb Header --}}
        <x-admin.breadcrumb-header 
            icon="fas fa-edit text-primary" 
            title="Edit Page" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.pages.list', 'label' => 'Pages'],
                ['label' => 'Edit']
            ]" 
        />

        <div class="card shadow border-0 mt-4">
            
            <!-- Header Section -->
            <div class="card-header bg-gradient text-white" 
                 style="background: linear-gradient(to right, #4f46e5, #9333ea, #ec4899);">
                <h5 class="mb-0">
                    <i class="fas fa-pen me-2"></i> Update Page Details
                </h5>
            </div>

            <div class="card-body">
                
                {{-- Flash Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> Please fix the following errors:
                        <ul class="mt-2 mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Form Section -->
                <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Title Fields -->
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-1 text-primary"></i> Title (English) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="title" 
                               value="{{ old('title', $page->title) }}" 
                               class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title_hi" class="form-label">
                            <i class="fas fa-heading me-1 text-danger"></i> Title (Hindi)
                        </label>
                        <input type="text" name="title_hi" id="title_hi" 
                               value="{{ old('title_hi', $page->title_hi) }}" 
                               class="form-control @error('title_hi') is-invalid @enderror">
                        @error('title_hi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Content Editors -->
                    <div class="mb-3">
                        <label for="body_eng" class="form-label">
                            <i class="fas fa-language me-1 text-primary"></i> Content (English) <span class="text-danger">*</span>
                        </label>
                        <textarea name="body_eng" id="body_eng" rows="8" 
                                  class="form-control tinymce-editor @error('body_eng') is-invalid @enderror" required>{{ old('body_eng', $page->body_eng) }}</textarea>
                        @error('body_eng')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="body_hindi" class="form-label">
                            <i class="fas fa-language me-1 text-success"></i> Content (Hindi)
                        </label>
                        <textarea name="body_hindi" id="body_hindi" rows="8" 
                                  class="form-control tinymce-editor @error('body_hindi') is-invalid @enderror">{{ old('body_hindi', $page->body_hindi) }}</textarea>
                        @error('body_hindi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SEO Section -->
                    <div class="border-top pt-3 mt-4">
                        <h6 class="mb-3">
                            <i class="fas fa-search me-2 text-purple"></i> SEO Settings
                        </h6>

                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" 
                                   value="{{ old('meta_title', $page->meta_title) }}" 
                                   class="form-control @error('meta_title') is-invalid @enderror">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3"
                                      class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $page->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" 
                                   value="{{ old('meta_keywords', $page->meta_keywords) }}" 
                                   class="form-control @error('meta_keywords') is-invalid @enderror">
                            @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Toggle -->
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="status" value="1" 
                               id="status" {{ old('status', $page->status) ? 'checked' : '' }}>
                        <label class="form-check-label" for="status">Active Page</label>
                    </div>

                    <!-- Submit Buttons -->
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i> Update Page
                    </button>
                    <a href="{{ route('admin.pages.list') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- TinyMCE -->
   
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '.tinymce-editor',
                promotion: false,
                branding: false,
                menubar: false,
                plugins: 'link lists table code help',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table | code help',
                skin: 'oxide',
                content_css: 'default',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        });
    </script>
</x-app-layout>
