<x-app-layout>
    {{-- ✅ LightGallery CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">

    <style>
        /* Folder & Gallery Styling */
        .month-folder {
            transition: transform 0.2s;
            cursor: pointer;
        }

        .month-folder:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .gallery-img-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            height: 160px;
            border: 1px solid #eee;
        }

        .gallery-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .gallery-img-wrapper:hover img {
            transform: scale(1.05);
        }

        .file-icon-wrapper {
            height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            transition: background 0.2s;
        }

        .file-icon-wrapper:hover {
            background: #e9ecef;
        }

        /* Theme Specific Colors */
        .folder-env i {
            color: #198754 !important;
        }

        /* Green for Environment */
        .folder-soc i {
            color: #0d6efd !important;
        }

        /* Blue for Social */
    </style>

    <div class="container py-4">

        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header icon="fas fa-file-alt text-primary mb-4" title="Package Project Documents"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                ['label' => $package->package_name],
                ['label' => 'Documents'],
            ]" />

        {{-- Alerts --}}
        @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible class="mb-3" />
        @endif
        @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible class="mb-3" />
        @endif

        {{-- ======================================================= --}}
        {{-- 📄 SECTION 1: STANDARD DOCUMENTS --}}
        {{-- ======================================================= --}}

        {{-- Package Level Documents --}}
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-folder-open text-primary me-2"></i> Package Documents</h5>
            </div>
            <div class="card-body">
                @if(count($documents))
                <div class="row g-3">
                    @foreach($documents as $doc)
                    <x-admin.document-card :doc="$doc" />
                    @endforeach
                </div>
                @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> No package-level documents available.
                </div>
                @endif
            </div>
        </div>

        {{-- SubProject Documents --}}
        @forelse($subProjectDocs as $spDoc)
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-project-diagram text-primary me-2"></i>
                    {{ $spDoc['subProject']->name }}
                    ({{ $spDoc['subProject']->packageProject->package_number ?? '-' }})
                </h5>
            </div>
            <div class="card-body">
                @if(count($spDoc['documents']))
                <div class="row g-3">
                    @foreach($spDoc['documents'] as $doc)
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
        @empty
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> No sub-projects found for this package project.
        </div>
        @endforelse


        {{-- ======================================================= --}}
        {{-- 🌿 SECTION 2: ENVIRONMENTAL SAFEGUARD GALLERY --}}
        {{-- ======================================================= --}}
        <div class="card shadow-sm border-0 mb-4" id="env-gallery-root">
            <div class="card-header bg-white py-3 border-bottom border-success border-2">
                <h5 class="mb-0 text-success fw-bold">
                    <i class="fas fa-leaf me-2"></i> Environmental Safeguard Gallery
                </h5>
            </div>

            <div class="card-body p-4">
                @if(empty($envGallery))
                <div class="text-center py-5">
                    <i class="fas fa-seedling fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted">No environmental media records found.</p>
                </div>
                @else
                {{-- Env Month Folders --}}
                <div class="month-list row g-4">
                    @php
                    $envMonths =
                    collect($envGallery)->keys()->map(fn($d)=>\Carbon\Carbon::parse($d)->format('Y-m'))->unique()->sortDesc();
                    @endphp
                    @foreach ($envMonths as $month)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card month-folder shadow-sm border h-100 text-center p-4" data-month="{{ $month }}">
                            <div class="mb-3 folder-env"><i class="fas fa-folder fa-4x"></i></div>
                            <h5 class="fw-bold text-dark mb-1">{{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
                            </h5>
                            <small class="text-muted">View Entries</small>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Env Details View (Hidden initially) --}}
                <div class="month-detail d-none">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <button class="btn btn-outline-success btn-sm px-3 back-btn">
                            <i class="fas fa-arrow-left me-2"></i> Back to Folders
                        </button>
                        <h4 class="month-title fw-bold text-success m-0"></h4>
                    </div>
                    <div class="month-content"></div>
                </div>
                @endif
            </div>
        </div>


        {{-- ======================================================= --}}
        {{-- 👥 SECTION 3: SOCIAL SAFEGUARD GALLERY --}}
        {{-- ======================================================= --}}
        <div class="card shadow-sm border-0 mb-4" id="soc-gallery-root">
            <div class="card-header bg-white py-3 border-bottom border-primary border-2">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="fas fa-users me-2"></i> Social Safeguard Gallery
                </h5>
            </div>

            <div class="card-body p-4">
                @if(empty($socGallery))
                <div class="text-center py-5">
                    <i class="fas fa-user-friends fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted">No social media records found.</p>
                </div>
                @else
                {{-- Soc Month Folders --}}
                <div class="month-list row g-4">
                    @php
                    $socMonths =
                    collect($socGallery)->keys()->map(fn($d)=>\Carbon\Carbon::parse($d)->format('Y-m'))->unique()->sortDesc();
                    @endphp
                    @foreach ($socMonths as $month)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card month-folder shadow-sm border h-100 text-center p-4" data-month="{{ $month }}">
                            <div class="mb-3 folder-soc"><i class="fas fa-folder fa-4x"></i></div>
                            <h5 class="fw-bold text-dark mb-1">{{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}
                            </h5>
                            <small class="text-muted">View Entries</small>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Soc Details View (Hidden initially) --}}
                <div class="month-detail d-none">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <button class="btn btn-outline-primary btn-sm px-3 back-btn">
                            <i class="fas fa-arrow-left me-2"></i> Back to Folders
                        </button>
                        <h4 class="month-title fw-bold text-primary m-0"></h4>
                    </div>
                    <div class="month-content"></div>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ✅ JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.min.js"></script>

    <script>
        // 1. Get Data from Controller (Ensure Controller passes these variables)
        const envData = @json($envGallery ?? []);
        const socData = @json($socGallery ?? []);

        /**
         * 2. Reusable Function to Initialize a Gallery Section
         * This handles the folder click, HTML generation, and LightGallery init.
         */
        function initGallerySection(rootId, data, themeColor) {
            const root = document.getElementById(rootId);
            
            // If section doesn't exist or has no data, skip
            if (!root || Object.keys(data).length === 0) return;

            const listDiv = root.querySelector('.month-list');
            const detailDiv = root.querySelector('.month-detail');
            const contentDiv = root.querySelector('.month-content');
            const titleEl = root.querySelector('.month-title');
            const backBtn = root.querySelector('.back-btn');

            // --- Handle Folder Click ---
            root.querySelectorAll('.month-folder').forEach(card => {
                card.addEventListener('click', () => {
                    const month = card.dataset.month;
                    
                    // Set Header Title
                    const dateObj = new Date(month + '-01');
                    titleEl.innerText = dateObj.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

                    let html = '';
                    
                    // Filter dates for this month and sort descending (Newest first)
                    const dates = Object.keys(data)
                        .filter(d => d.startsWith(month))
                        .sort().reverse();

                    dates.forEach(date => {
                        const items = data[date];
                        const displayDate = new Date(date).toLocaleDateString('en-US', { weekday: 'short', day: 'numeric', month: 'short' });

                        html += `<div class="mb-5">
                                    <h5 class="border-start   ps-3 mb-3 fw-bold text-dark">${displayDate}</h5>`;

                        items.forEach((group, idx) => {
                            // Logic for Status Badge
                            let statusBadge = '';
                            if(group.yes_no === 1) statusBadge = '<span class="badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> Complied</span>';
                            else if(group.yes_no === 2) statusBadge = '<span class="badge bg-danger text-white"><i class="fas fa-times-circle me-1"></i> Not Complied</span>';
                            else statusBadge = '<span class="badge bg-secondary text-white">N/A</span>';

                            html += `
                            <div class="card mb-3 border bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Left Side: Details --}}
                                        <div class="col-md-2 border-end">
                                            <div class="mb-2">
                                                <h6 class="fw-bold text-dark mb-2">${group.item_description}</h6>
                                            </div>
                                            <div class="h5 text-muted mb-2">
                                                <strong>Phase:</strong> ${group.phase} <br>
                                                <strong>Status:</strong> ${statusBadge}
                                            </div>
                                            <p class="h5 text-secondary fst-italic border-top pt-2">
                                                "${group.remarks || 'No remarks provided.'}"
                                            </p>
                                        </div>

                                        {{-- Right Side: Media Grid --}}
                                        <div class="col-md-7">
                                            <div class="row g-2 lightgallery-container">`;

                            group.media.forEach(media => {
                                if (media.is_image) {
                                    // Image Block
                                    html += `
                                    <div class="col-xl-3 col-lg-4 col-6">
                                        <a href="${media.full_url}" class="gallery-item" data-sub-html="<h4>${group.item_description}</h4>">
                                            <div class="gallery-img-wrapper shadow-sm">
                                                <img src="${media.full_url}" alt="${media.original_name}">
                                            </div>
                                        </a>
                                    </div>`;
                                } else {
                                    // PDF/File Block
                                    html += `
                                    <div class="col-xl-3 col-lg-4 col-6">
                                        <a href="${media.full_url}" target="_blank" class="text-decoration-none">
                                            <div class="file-icon-wrapper shadow-sm border rounded">
                                                <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                                <span class="small text-muted text-truncate w-75 text-center" title="${media.original_name}">
                                                    ${media.extension.toUpperCase()}
                                                </span>
                                            </div>
                                        </a>
                                    </div>`;
                                }
                            });

                            html += `       </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                        html += `</div>`;
                    });

                    contentDiv.innerHTML = html;

                    // --- Initialize LightGallery for this block ---
                    contentDiv.querySelectorAll('.lightgallery-container').forEach(el => {
                        lightGallery(el, {
                            plugins: [lgZoom, lgThumbnail],
                            speed: 500,
                            selector: '.gallery-item', // Only images open in Lightbox
                            download: true
                        });
                    });

                    // Switch to Detail View
                    listDiv.classList.add('d-none');
                    detailDiv.classList.remove('d-none');
                });
            });

            // --- Handle Back Button ---
            backBtn.addEventListener('click', () => {
                detailDiv.classList.add('d-none');
                listDiv.classList.remove('d-none');
                contentDiv.innerHTML = ''; // Clear content to save memory
            });
        }

        // 3. Initialize both galleries independently on load
        document.addEventListener('DOMContentLoaded', () => {
            initGallerySection('env-gallery-root', envData, 'success'); // Green Theme
            initGallerySection('soc-gallery-root', socData, 'primary'); // Blue Theme
        });

    </script>
</x-app-layout>