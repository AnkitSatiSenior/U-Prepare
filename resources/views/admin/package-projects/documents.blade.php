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

        {{-- ---------------- Social Safeguard Gallery ---------------- --}}
        @if(!empty($gallery))
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-images text-primary me-2"></i> Social Safeguard Media Gallery</h5>
                </div>
                <div class="card-body">

                    {{-- Month folders --}}
                    <div id="month-list" class="row g-3">
                        @php
                            $months = collect($gallery)
                                ->keys()
                                ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m'))
                                ->unique();
                        @endphp
                        @foreach ($months as $month)
                            <div class="col-md-3 col-6">
                                <div class="month-card p-4 border rounded shadow-sm text-center bg-white h-100 d-flex flex-column justify-content-center align-items-center"
                                    style="cursor:pointer;" data-month="{{ $month }}">
                                    <i class="fas fa-folder fa-3x text-warning mb-2"></i>
                                    <h5 class="m-0">{{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h5>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Month detail view --}}
                    <div id="month-detail" class="d-none">
                        <button id="back-to-months" class="btn btn-secondary mb-3">⬅ Back to months</button>
                        <div id="month-content"></div>
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
        const galleryData = @json($gallery ?? []);

        const monthList = document.getElementById('month-list');
        const monthDetail = document.getElementById('month-detail');
        const monthContent = document.getElementById('month-content');
        const backBtn = document.getElementById('back-to-months');

        document.querySelectorAll('.month-card').forEach(card => {
            card.addEventListener('click', () => {
                const month = card.dataset.month;
                let html = '';

                for (const [date, items] of Object.entries(galleryData)) {
                    if (!date.startsWith(month)) continue;

                    html += `<h4 class="mt-4">📅 ${new Date(date).toLocaleDateString()}</h4>`;

                    items.forEach((group, idx) => {
                        html += `<div class="mb-3 p-3 border rounded bg-white shadow-sm">
                                    <p><strong>Item:</strong> ${group.item_description ?? 'N/A'}</p>
                                    <p><strong>Status:</strong> ${
                                        group.yes_no === 1 ? '✅ Yes' :
                                        group.yes_no === 2 ? '❌ No' : '⚪ N/A'
                                    }</p>
                                    <p><strong>Remarks:</strong> ${group.remarks ?? '-'}</p>
                                    <div id="lightgallery-${month}-${date}-${idx}" class="row g-3 lightgallery">`;

                        group.media.forEach(media => {
                            const path = `/storage/app/public/${media.path}`;
                            if (media.type.includes('image')) {
                                html += `<div class="col-md-3 col-6">
                                            <a href="${path}" data-lg-size="1600-1067">
                                                <img src="${path}" class="img-fluid rounded shadow-sm" style="height:200px;object-fit:cover;">
                                            </a>
                                        </div>`;
                            } else {
                                html += `<div class="col-md-3 col-6">
                                            <a href="${path}" target="_blank">
                                                <div class="d-flex align-items-center justify-content-center bg-light border rounded" style="height:200px;">
                                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                </div>
                                            </a>
                                        </div>`;
                            }
                        });

                        html += `</div></div>`;
                    });
                }

                monthContent.innerHTML = html;

                document.querySelectorAll('.lightgallery').forEach(gallery => {
                    lightGallery(gallery, {
                        plugins: [lgZoom, lgThumbnail],
                        speed: 500,
                        selector: 'a[href$=".jpg"],a[href$=".png"],a[href$=".jpeg"],a[href$=".gif"],a[href$=".webp"]'
                    });
                });

                monthList.classList.add('d-none');
                monthDetail.classList.remove('d-none');
            });
        });

        backBtn.addEventListener('click', () => {
            monthDetail.classList.add('d-none');
            monthList.classList.remove('d-none');
            monthContent.innerHTML = '';
        });
    </script>
</x-app-layout>
