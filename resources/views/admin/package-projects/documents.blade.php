<x-app-layout>
    {{-- ✅ LightGallery CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">

    <div class="container py-4">

        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header 
            icon="fas fa-file-alt text-primary mb-4" 
            title="Package Project Documents"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                ['label' => $package->package_name],
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

        {{-- ---------------- Package Documents ---------------- --}}
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

        {{-- ---------------- SubProject Documents ---------------- --}}
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
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No sub-projects found for this package project.
            </div>
        @endforelse

        {{-- ---------------- Safeguard Gallery ---------------- --}}
@if(!empty($gallery))
<div class="card shadow-sm border mb-4" id="soc-gallery-root">
    <div class="card-header bg-white py-3 border-bottom border-primary border-2">
        <h5 class="mb-0 text-primary fw-bold">
            <i class="fas fa-users me-2"></i> Safeguard Gallery
        </h5>
    </div>
    
    <div class="card-body p-4">
        
        {{-- LEVEL 1: COMPLIANCE FOLDERS --}}
        <div id="compliance-list" class="row g-4">
            @foreach($gallery as $complianceName => $months)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card folder-card shadow-sm border h-100 text-center p-4 compliance-folder" 
                         style="cursor:pointer; transition: transform 0.2s;"
                         data-compliance="{{ $complianceName }}">
                        
                        <div class="mb-3 text-primary"><i class="fas fa-folder fa-4x"></i></div>
                        <h6 class="fw-bold text-dark mb-1">{{ $complianceName }}</h6>
                        <span class="badge bg-light text-secondary border">
                            {{ count($months) }} Months
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- LEVEL 2: MONTH FOLDERS (Hidden Initially) --}}
        <div id="month-list" class="d-none">
            <div class="d-flex align-items-center mb-4">
                <button id="back-to-compliance" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <h5 class="m-0 text-primary fw-bold" id="selected-compliance-title"></h5>
            </div>
            <div id="month-grid" class="row g-4"></div>
        </div>

        {{-- LEVEL 3: GALLERY DETAILS (Hidden Initially) --}}
        <div id="gallery-view" class="d-none">
            <div class="d-flex align-items-center mb-4">
                <button id="back-to-months" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <div>
                    <h5 class="m-0 text-primary fw-bold" id="gallery-title-month"></h5>
                    <small class="text-muted" id="gallery-title-compliance"></small>
                </div>
            </div>
            <div id="gallery-content"></div>
        </div>

    </div>
</div>
@endif

    </div>

    {{-- ✅ LightGallery JS --}}
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data passed from Controller
        const galleryData = @json($gallery ?? []);
        
        // DOM Elements
        const complianceList = document.getElementById('compliance-list');
        const monthList = document.getElementById('month-list');
        const monthGrid = document.getElementById('month-grid');
        const galleryView = document.getElementById('gallery-view');
        const galleryContent = document.getElementById('gallery-content');
        
        // Titles
        const complianceTitle = document.getElementById('selected-compliance-title');
        const galleryTitleMonth = document.getElementById('gallery-title-month');
        const galleryTitleCompliance = document.getElementById('gallery-title-compliance');

        // Back Buttons
        const btnBackToCompliance = document.getElementById('back-to-compliance');
        const btnBackToMonths = document.getElementById('back-to-months');

        // State Variables
        let currentCompliance = null;
        let currentMonth = null;

        // --- STEP 1: Click Compliance Folder ---
        document.querySelectorAll('.compliance-folder').forEach(folder => {
            folder.addEventListener('click', function() {
                currentCompliance = this.dataset.compliance;
                const monthsData = galleryData[currentCompliance];

                // Setup Header
                complianceTitle.textContent = currentCompliance;

                // Generate Month Folders
                let html = '';
                // Sort months descending (Newest first)
                const sortedMonths = Object.keys(monthsData).sort().reverse();

                sortedMonths.forEach(monthKey => {
                    // Format Date (YYYY-MM -> Month Year)
                    const dateObj = new Date(monthKey + '-01');
                    const monthName = dateObj.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                    
                    // Count total items in this month
                    let itemCount = 0;
                    Object.values(monthsData[monthKey]).forEach(arr => itemCount += arr.length);

                    html += `
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card folder-card shadow-sm border h-100 text-center p-4 month-folder" 
                             style="cursor:pointer; transition: transform 0.2s;"
                             data-month-key="${monthKey}">
                            <div class="mb-3 text-warning"><i class="fas fa-folder-open fa-4x"></i></div>
                            <h6 class="fw-bold text-dark mb-1">${monthName}</h6>
                            <small class="text-muted">${itemCount} Entries</small>
                        </div>
                    </div>`;
                });

                monthGrid.innerHTML = html;

                // Attach Events to New Month Folders
                attachMonthClickEvents(monthsData);

                // Switch Views
                complianceList.classList.add('d-none');
                monthList.classList.remove('d-none');
            });
        });

        // --- STEP 2: Click Month Folder ---
        function attachMonthClickEvents(monthsData) {
            document.querySelectorAll('.month-folder').forEach(folder => {
                folder.addEventListener('click', function() {
                    const monthKey = this.dataset.monthKey;
                    const daysData = monthsData[monthKey];
                    
                    // Setup Header
                    const dateObj = new Date(monthKey + '-01');
                    galleryTitleMonth.textContent = dateObj.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                    galleryTitleCompliance.textContent = currentCompliance;

                    let html = '';
                    
                    // Sort days descending
                    const sortedDays = Object.keys(daysData).sort().reverse();

                    sortedDays.forEach(date => {
                        const items = daysData[date];
                        const displayDate = new Date(date).toLocaleDateString('en-US', { weekday: 'long', day: 'numeric', month: 'long' });

                        html += `<div class="mb-5">
                                    <h5 class="border-start  ps-3 mb-3 fw-bold text-dark bg-light py-2 rounded">${displayDate}</h5>`;

                        items.forEach(item => {
                            // Status Logic
                            let statusBadge = '';
                            if(item.yes_no === 1) statusBadge = '<span class="badge bg-success text-white"><i class="fas fa-check-circle"></i> Yes</span>';
                            else if(item.yes_no === 0) statusBadge = '<span class="badge bg-danger text-white"><i class="fas fa-times-circle"></i> No</span>';
                            else statusBadge = '<span class="badge bg-secondary text-white">N/A</span>';

                            html += `
                            <div class="card mb-3 border shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 border-end">
                                            <h6 class="fw-bold text-dark">${item.item_description}</h6>
                                            <div class="mt-2 text-muted h5">
                                                <div><strong>Phase:</strong> ${item.phase}</div>
                                                <div class="mt-1"><strong>Status:</strong> ${statusBadge}</div>
                                            </div>
                                            <div class="mt-3 p-2 bg-light rounded border border-light small fst-italic">
                                                "${item.remarks || 'No remarks provided.'}"
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row g-2 lightgallery-container">`;

                            item.media.forEach(media => {
                                const path = `/storage/app/public/${media.path}`; // Adjust if needed
                                if (media.type.includes('image')) {
                                    html += `<div class="col-md-3 col-6">
                                                <a href="${path}" data-lg-size="1600-1067" class="gallery-item">
                                                    <img src="${path}" class="img-fluid rounded border" style="height:120px; width:100%; object-fit:cover;">
                                                </a>
                                            </div>`;
                                } else {
                                    html += `<div class="col-md-3 col-6">
                                                <a href="${path}" target="_blank">
                                                    <div class="d-flex align-items-center justify-content-center bg-light border rounded" style="height:120px;">
                                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>`;
                                }
                            });

                            html += `</div></div></div></div></div>`;
                        });
                        html += `</div>`;
                    });

                    galleryContent.innerHTML = html;

                    // Initialize LightGallery
                    document.querySelectorAll('.lightgallery-container').forEach(el => {
                        lightGallery(el, {
                            plugins: [lgZoom, lgThumbnail],
                            speed: 500,
                            selector: '.gallery-item'
                        });
                    });

                    // Switch Views
                    monthList.classList.add('d-none');
                    galleryView.classList.remove('d-none');
                });
            });
        }

        // --- Back Buttons ---
        btnBackToCompliance.addEventListener('click', () => {
            monthList.classList.add('d-none');
            complianceList.classList.remove('d-none');
        });

        btnBackToMonths.addEventListener('click', () => {
            galleryView.classList.add('d-none');
            monthList.classList.remove('d-none');
            galleryContent.innerHTML = ''; // Clear memory
        });
    });
</script>
</x-app-layout>