<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        <x-admin.breadcrumb-header
    icon="fas fa-bars text-primary"
    title="Create Navbar Item"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['route' => 'admin.navbar-items.index', 'label' => 'Navbar Items'],
        ['label' => 'Create']
    ]"
/>


        <!-- Session Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Whoops!</strong> Please fix the following errors:
                <ul class="mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i> New Navigation Item
                </h5>
                <a href="{{ route('admin.navbar-items.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.navbar-items.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Home" value="{{ old('title') }}" required>
                            <div class="invalid-feedback">Please enter a title.</div>
                        </div>

                        <!-- Slug -->
                        <div class="col-md-6 mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="slug" name="slug" class="form-control" placeholder="home" value="{{ old('slug') }}" required>
                                <button type="button" class="btn btn-outline-primary" onclick="generateSlug()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please provide a slug.</div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Parent Item -->
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">Parent Item</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">None (Top Level)</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Order -->
                        <div class="col-md-6 mb-3">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" id="order" name="order" class="form-control" value="{{ old('order', 0) }}">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Is Dropdown -->
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="is_dropdown" value="0">
                                <input class="form-check-input" type="checkbox" id="is_dropdown" name="is_dropdown" value="1">
                                <label class="form-check-label" for="is_dropdown">Is Dropdown</label>
                            </div>
                        </div>

                        <!-- Is Active -->
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Is Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Route Name -->
                        <div class="col-md-6 mb-3">
                            <label for="route" class="form-label">Route Name</label>
                            <input type="text" id="route" name="route" class="form-control" placeholder="home.index" value="{{ old('route') }}">
                        </div>

                        <!-- Custom URL -->
                        <div class="col-md-6 mb-3">
                            <label for="url" class="form-label">Custom URL</label>
                            <input type="text" id="url" name="url" class="form-control" placeholder="/home" value="{{ old('url') }}">
                        </div>
                    </div>

                    <!-- Icon Class -->
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon Class</label>
                        <div class="input-group">
                            <input type="text" id="icon" name="icon" class="form-control" placeholder="fas fa-home" value="{{ old('icon') }}">
                            <span class="input-group-text"><i class="fas fa-icons"></i></span>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end border-top pt-3">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Create Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Slug Generator Script -->
    <script>
        function generateSlug() {
            const title = document.getElementById('title').value;
            if (title) {
                const slug = title.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = slug;
            }
        }

        // Auto-generate slug on title input if slug is empty
        document.getElementById('title').addEventListener('input', function () {
            if (!document.getElementById('slug').value) {
                generateSlug();
            }
        });
    </script>
</x-app-layout>
