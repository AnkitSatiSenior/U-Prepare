<x-app-layout>
    {{-- ✅ LightGallery CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">

    <div class="container py-4">

        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header 
            icon="fas fa-file-alt text-primary mb-4" 
            title="SubProject Documents"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                ['route' => 'admin.sub-projects.documents', 'params' => $subProject->id, 'label' => $subProject->name],
                ['label' => 'Documents'],
            ]" 
        />

        {{-- Alerts --}}
        @if(session('success'))
            <x-alert type="success" :message="session('success')" dismissible class="mb-3"/>
        @endif
        @if(session('error'))
            <x-alert type="danger" :message="session('error')" dismissible class="mb-3"/>
        @endif

        {{-- ---------------- SubProject Info ---------------- --}}
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-project-diagram text-primary me-2"></i>
                    {{ $subProject->name }} 
                    <small class="text-muted">({{ $subProject->packageProject->package_number ?? '-' }})</small>
                </h5>
            </div>
            <div class="card-body">
                <p><strong>Parent Package:</strong> {{ $subProject->packageProject->package_name ?? '-' }}</p>
                <p><strong>SubProject ID:</strong> {{ $subProject->id }}</p>
            </div>
        </div>

        {{-- ---------------- Documents ---------------- --}}
        <div class="card shadow-sm border">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-folder-open text-primary me-2"></i> Documents</h5>
            </div>
            <div class="card-body">
                @if(count($documents))
                    <div class="row g-3">
                        @foreach($documents as $doc)
                            {{-- Reuse your document-card component --}}
                            <x-admin.document-card :doc="$doc" />
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> No documents available for this sub-project.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ✅ LightGallery JS --}}
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.min.js"></script>

    <script>
        document.querySelectorAll('.lightgallery').forEach(gallery => {
            lightGallery(gallery, {
                plugins: [lgZoom, lgThumbnail],
                speed: 500,
                selector: 'a[href$=".jpg"],a[href$=".png"],a[href$=".jpeg"],a[href$=".gif"],a[href$=".webp"]'
            });
        });
    </script>
</x-app-layout>
