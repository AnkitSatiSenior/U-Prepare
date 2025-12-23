<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4 h2">📂 Media Gallery</h2>

        {{-- 🔍 Search Form --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.media.gallery') }}" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search files..."
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Search
                    </button>
                    @if (request('search'))
                        <a href="{{ route('admin.media.gallery') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- 📤 Upload Form --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form id="uploadForm" method="POST" action="{{ route('admin.media.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="media_files[]" multiple class="form-control mb-3" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Upload
                    </button>
                </form>
            </div>
        </div>

        {{-- 📂 Gallery Items --}}
        @foreach ($filesGrouped as $month => $files)
            <div class="row g-3 mb-5" id="gallery">
                <div class="col-12">
                    <h3 class="mt-3 h3">{{ $month }}</h3>
                </div>

                @foreach ($files as $file)
                    <div class="col-md-3 col-6">
                        <div class="card shadow-sm h-100">
                            {{-- Thumbnail / Preview --}}
                            <a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal"
                                data-index="{{ $loop->parent->index * $files->count() + $loop->index }}">
                                @if ($file['isImage'])
                                    <img src="{{ $file['thumb'] }}" alt="{{ $file['filename'] }}"
                                        class="img-fluid rounded-top"
                                        style="height:200px; width:100%; object-fit:cover; background:#f9f9f9;"
                                        loading="lazy">
                                @elseif ($file['extension'] === 'pdf')
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded-top"
                                        style="height:200px;">
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded-top"
                                        style="height:200px;">
                                        <i class="fas fa-file fa-3x text-secondary"></i>
                                    </div>
                                @endif
                            </a>

                            {{-- Actions --}}
                            <div class="card-body p-2 text-center">
                                <small class="d-block text-truncate">{{ $file['filename'] }}</small>
                                <div class="btn-group mt-2">
                                    <button class="btn btn-sm btn-outline-secondary copy-btn"
                                        data-url="{{ $file['url'] }}">
                                        <i class="fa fa-link"></i>
                                    </button>
                                    <a href="{{ $file['url'] }}" download class="btn btn-sm btn-outline-success">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                        data-id="{{ $file['id'] }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- Hidden Delete Form --}}
        <form id="delete-file-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $filesPaginator->links('pagination::bootstrap-4') }}
        </div>

        {{-- Modal Carousel --}}
        <div class="modal fade" id="galleryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content bg-dark">
                    <div class="modal-body p-0">
                        <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach ($allFiles as $index => $file)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        @if ($file['isImage'])
                                            <img src="{{ $file['url'] }}" class="d-block w-100"
                                                style="max-height:80vh; object-fit:contain;">
                                        @elseif ($file['extension'] === 'pdf')
                                            <iframe src="{{ $file['url'] }}" class="w-100"
                                                style="height:80vh; border:none;"></iframe>
                                        @else
                                            <div class="d-flex justify-content-center align-items-center text-white"
                                                style="height:80vh;">
                                                <i class="fas fa-file fa-4x"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer bg-dark flex-column">
                        <div class="d-flex overflow-auto w-100 mb-2" style="gap:10px;">
                            @foreach ($allFiles as $index => $file)
                                <div style="cursor:pointer; flex:0 0 auto;" data-bs-target="#galleryCarousel"
                                    data-bs-slide-to="{{ $index }}">
                                    @if ($file['isImage'])
                                        <img src="{{ $file['thumb'] }}" alt="{{ $file['filename'] }}"
                                            style="height:60px; width:80px; object-fit:cover; border:2px solid #fff; border-radius:5px;">
                                    @elseif ($file['extension'] === 'pdf')
                                        <div class="d-flex align-items-center justify-content-center bg-light text-danger"
                                            style="height:60px; width:80px; border-radius:5px;">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light text-secondary"
                                            style="height:60px; width:80px; border-radius:5px;">
                                            <i class="fas fa-file"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const uploadForm = document.getElementById('uploadForm');
            const deleteForm = document.getElementById('delete-file-form');
            const deleteRouteTemplate = @json(route('admin.media.delete', ':id'));

            // --------------------------
            // 📤 Upload
            // --------------------------
            if (uploadForm) {
                uploadForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    try {
                        const res = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        });

                        const contentType = res.headers.get("content-type");
                        if (contentType && contentType.includes("application/json")) {
                            const result = await res.json();
                            alert(result.message || "Files uploaded successfully!");
                            location.reload();
                        } else {
                            // fallback redirect
                            window.location.href = window.location.href;
                        }
                    } catch (err) {
                        console.error(err);
                        alert("Upload failed.");
                    }
                });
            }

            // --------------------------
            // 🔗 Copy link
            // --------------------------
            document.addEventListener('click', e => {
                const btn = e.target.closest('.copy-btn');
                if (!btn) return;
                const url = btn.dataset.url;
                navigator.clipboard.writeText(url).then(() => alert("Link copied!"));
            });

            // --------------------------
            // 🎞 Carousel
            // --------------------------
            document.querySelectorAll('#gallery a[data-index]').forEach(el => {
                el.addEventListener('click', e => {
                    const index = parseInt(e.currentTarget.dataset.index, 10);
                    const carousel = document.querySelector('#galleryCarousel');
                    bootstrap.Carousel.getOrCreateInstance(carousel).to(index);
                });
            });

            // --------------------------
            // 🗑 Delete
            // --------------------------
            document.addEventListener('click', e => {
                const btn = e.target.closest('.delete-btn');
                if (!btn) return;
                const fileId = btn.dataset.id;
                if (!fileId || !confirm("Are you sure you want to delete this file?")) return;

                deleteForm.action = deleteRouteTemplate.replace(':id', fileId);
                deleteForm.submit();
            });
        });
    </script>
</x-app-layout>
