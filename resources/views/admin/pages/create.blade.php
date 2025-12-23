{{-- resources/views/admin/pages/create.blade.php --}}
<x-app-layout>
    <div class="container py-4">

        {{-- Breadcrumb Header --}}
        <x-admin.breadcrumb-header 
            icon="fas fa-boxes text-primary" 
            title="Create New Package Project" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                ['label' => 'Create']
            ]" 
        />

        <div class="card shadow border-0 mt-4">
            
            <!-- Header Section -->
            <div class="card-header bg-gradient text-white" 
                 style="background: linear-gradient(to right, #4f46e5, #9333ea, #ec4899);">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i> Page Details
                </h5>
            </div>

            <div class="card-body">
                
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> There were some errors with your submission:
                        <ul class="mt-2 mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Section -->
                <form action="{{ route('admin.pages.create') }}" method="POST">
                    @csrf

                    <!-- Title Fields -->
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-1 text-primary"></i> Title (English)
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                               class="form-control" required placeholder="Enter page title in English">
                    </div>

                    <div class="mb-3">
                        <label for="title_hi" class="form-label">
                            <i class="fas fa-heading me-1 text-danger"></i> Title (Hindi)
                        </label>
                        <input type="text" name="title_hi" id="title_hi" value="{{ old('title_hi') }}" 
                               class="form-control" required placeholder="Enter page title in Hindi">
                    </div>

                    <!-- Content Editors -->
                    <div class="mb-3">
                        <label for="body_eng" class="form-label">
                            <i class="fas fa-language me-1 text-primary"></i> Content (English)
                        </label>
                        <textarea name="body_eng" id="body_eng" rows="8" class="form-control tinymce-editor" required>{{ old('body_eng') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="body_hindi" class="form-label">
                            <i class="fas fa-language me-1 text-success"></i> Content (Hindi)
                        </label>
                        <textarea name="body_hindi" id="body_hindi" rows="8" class="form-control tinymce-editor" required>{{ old('body_hindi') }}</textarea>
                    </div>

                    <!-- SEO Section -->
                    <div class="border-top pt-3 mt-4">
                        <h6 class="mb-3">
                            <i class="fas fa-search me-2 text-purple"></i> SEO Settings
                        </h6>

                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                                   class="form-control" placeholder="Optional meta title for SEO">
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3"
                                      class="form-control" placeholder="Optional meta description for SEO">{{ old('meta_description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}"
                                   class="form-control" placeholder="Optional, comma separated keywords">
                        </div>
                    </div>

                    <!-- Status Toggle -->
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="status" value="active" 
                               id="status" {{ old('status', $page->status ?? '') === 'active' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status">Active Page</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i> Create Page
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
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
