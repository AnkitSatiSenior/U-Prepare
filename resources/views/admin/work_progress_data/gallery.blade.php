<x-app-layout>
    <div class="container py-4">

        <x-admin.breadcrumb-header icon="fas fa-images text-primary" :title="'Work Progress Gallery - ' . ($project->name ?? 'Unnamed Project')" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i> Dashboard'],
            ['label' => 'Work Progress Gallery'],
        ]" />

        <h4 class="text-primary fw-bold mb-4">
            <i class="fas fa-camera me-2"></i> Project: {{ $project->name ?? 'N/A' }}
        </h4>

        @forelse ($groupedMedia as $key => $mediaItems)
            @php
                [$componentName, $componentDetails] = explode('||', $key);
                $galleryId = 'gallery-' . md5($key);
            @endphp

            {{-- Component Heading --}}
            <div class="mb-3 mt-5">
                <h5 class="fw-bold text-info">
                    <i class="fas fa-cubes me-2"></i>
                    {{ $componentName }} – {{ $componentDetails }}
                </h5>
                <hr>
            </div>

            {{-- Gallery --}}
            <div class="row g-3" id="{{ $galleryId }}">
                @foreach ($mediaItems as $media)
                    <div class="col-md-4 col-lg-3 mb-2">
                        <a href="{{ $media['path'] }}"
                            data-sub-html="
                                <h6>{{ $componentName }} – {{ $componentDetails }}</h6>
                                <p>{{ $media['description'] }}</p>
                                <small>By {{ $media['uploaded_by'] }} | {{ $media['uploaded_at'] }}</small>
                           ">

                            <div class="card border-0 shadow-sm h-100">
                                <div class="progress-img-box">
                                    <img src="{{ $media['path'] }}" alt="Progress Image" >
                                </div>

                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Init LightGallery --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    lightGallery(document.getElementById('{{ $galleryId }}'), {
                        selector: 'a',
                        plugins: [lgZoom, lgThumbnail, lgFullscreen],
                        speed: 400,
                        thumbnail: true,
                        zoom: true,
                        fullscreen: true
                    });
                });
            </script>

        @empty
            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-circle me-2"></i>
                No images found for this project yet.
            </div>
        @endforelse

    </div>
    {{-- LightGallery CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">

    {{-- LightGallery JS --}}
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/fullscreen/lg-fullscreen.min.js"></script>
    <style>
        .progress-img-box {
            width: 100%;
            height: 220px;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 20px !important;
        }

        .progress-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            margin-bottom: 20px !important;
        }
    </style>
</x-app-layout>
