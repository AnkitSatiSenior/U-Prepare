<x-app-layout>
    {{-- ✅ LightGallery CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">

    <div class="container py-4">
        <h2 class="mb-3 h2">📂 Media Gallery</h2>
        <div class="mb-4 p-3 bg-light border rounded h2">
            <p class="mb-1"><strong>Project:</strong> {{ $subProject->name }}
            </p>
            <p class="mb-1">
                <strong>Package Number:</strong>
                {{ $subProject->packageProject->package_number ?? '-' }}

            </p>

            <p class="mb-1"><strong>Compliance:</strong> {{ $compliance->name }}</p>

            {{-- ✅ Phase Filter --}}
            <form method="GET" action="{{ route('admin.social_safeguard.gallery') }}" class="mt-2">
                <input type="hidden" name="sub_package_project_id" value="{{ $subProject->id }}">
                <input type="hidden" name="safeguard_compliance_id" value="{{ $compliance->id }}">

                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="phase" class="col-form-label"><strong>Phase:</strong></label>
                    </div>
                    <div class="col-auto">
                        <select name="contraction_phase_id" id="phase" class="form-control"
                            onchange="this.form.submit()">
                            <option value="">All Phases</option>
                            @foreach ($compliance->contractionPhases as $phase)
                                <option value="{{ $phase->id }}" {{ $phase->id == $phaseId ? 'selected' : '' }}>
                                    {{ $phase->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            {{-- ✅ Show active phase name if filtered --}}
            @if ($phaseId)
                <p class="mt-2"><strong>Selected Phase:</strong>
                    {{ \App\Models\ContractionPhase::find($phaseId)?->name }}</p>
            @endif
        </div>


        {{-- ✅ Month folders --}}
        <div id="month-list" class="row g-3">
            @php
                // group by month-year
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
                        <h5 class="m-0">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h5>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ✅ Month detail view --}}
        <div id="month-detail" class="d-none">
            <button id="back-to-months" class="btn btn-secondary mb-3">⬅ Back to months</button>
            <div id="month-content"></div>
        </div>
    </div>

    {{-- ✅ LightGallery JS --}}
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.min.js"></script>

    <script>
        const galleryData = @json($gallery); // ✅ pass data to JS

        const monthList = document.getElementById('month-list');
        const monthDetail = document.getElementById('month-detail');
        const monthContent = document.getElementById('month-content');
        const backBtn = document.getElementById('back-to-months');

        // Show month content
        document.querySelectorAll('.month-card').forEach(card => {
            card.addEventListener('click', () => {
                const month = card.dataset.month;

                // filter gallery by this month
                let html = '';
                for (const [date, items] of Object.entries(galleryData)) {
                    if (!date.startsWith(month)) continue;

                    html +=
                        `<h4 class="mt-4">📅 ${new Date(date).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' })}</h4>`;

                    items.forEach((group, idx) => {
                        html += `
                        <div class="mb-3 p-3 border rounded bg-white shadow-sm h2">
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
                                html += `
                                <div class="col-md-3 col-6">
                                    <a href="${path}" data-lg-size="1600-1067" data-sub-html="<p>${media.meta_data?.name ?? 'File'}</p>">
                                        <img src="${path}" class="img-fluid rounded shadow-sm" style="height:200px;object-fit:cover;">
                                    </a>
                                </div>`;
                            } else if (media.type.includes('pdf')) {
                                html += `
                                <div class="col-md-3 col-6">
                                    <a href="${path}" target="_blank">
                                        <div class="d-flex align-items-center justify-content-center bg-light border rounded" style="height:200px;">
                                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        </div>
                                    </a>
                                </div>`;
                            } else {
                                html += `
                                <div class="col-md-3 col-6">
                                    <a href="${path}" target="_blank">
                                        <div class="d-flex align-items-center justify-content-center bg-light border rounded" style="height:200px;">
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                        </div>
                                    </a>
                                </div>`;
                            }
                        });

                        html += `</div></div>`;
                    });
                }

                monthContent.innerHTML = html;

                // re-init lightGallery
                document.querySelectorAll('.lightgallery').forEach(gallery => {
                    lightGallery(gallery, {
                        plugins: [lgZoom, lgThumbnail],
                        speed: 500,
                        selector: 'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],a[href$=".webp"]'
                    });
                });

                // toggle views
                monthList.classList.add('d-none');
                monthDetail.classList.remove('d-none');
            });
        });

        // Back button
        backBtn.addEventListener('click', () => {
            monthDetail.classList.add('d-none');
            monthList.classList.remove('d-none');
            monthContent.innerHTML = '';
        });
    </script>
</x-app-layout>
