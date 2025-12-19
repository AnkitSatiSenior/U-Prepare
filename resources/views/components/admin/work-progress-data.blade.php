<x-admin.card title="Work Programs" icon="fas fa-tasks" headerClass="bg-warning text-dark fw-bold">

    @foreach ($subProjectsData as $sp)
    <div class="mb-4">
        <h5 class="fw-bold text-primary d-flex align-items-center">
            <i class="fas fa-cube me-2"></i> {{ $sp['name'] }}
        </h5>

        @if (empty($sp['components']) || $sp['components']->isEmpty())
        <p class="text-muted fst-italic ms-4">
            <i class="fas fa-info-circle me-1"></i> No Work Progress found.
        </p>
        @else
        <x-admin.data-table :id="'work-program-' . $sp['id']" :headers="[
                'ID',
                'Component',
                'Type/Details',
                'Side/Location',
                'Stage',
                '% Progress',
                'Remarks',
                'Images'
            ]" :pageLength="20" :excel="true" :print="true" :resourceName="'work-program-' . $sp['id']">
            @foreach ($sp['components'] as $component)
            @php
            $entryData = $sp['existingEntries']->get($component->id);
            $totalProgress = $entryData->total_progress ?? 0;
            $lastEntry = $entryData->last_entry ?? null;
            @endphp
            <tr>
                <td>{{ $component->id }}</td>
                <td>{{ $component->work_component }}</td>
                <td>{{ $component->type_details ?? '-' }}</td>
                <td>{{ $component->side_location ?? '-' }}</td>
                <td>{{ $lastEntry->current_stage ?? '-' }}</td>
                <td>{{ $totalProgress }}%</td>
                <td>{{ $lastEntry->remarks ?? '-' }}</td>
                <td>
    @if(!empty($lastEntry->images))
        <button
            type="button"
            class="btn btn-sm btn-primary open-gallery"
            data-gallery-id="gallery-{{ $component->id }}"
        >
            View Images ({{ count($lastEntry->images) }})
        </button>

        {{-- Hidden gallery container --}}
        <div id="gallery-{{ $component->id }}" class="d-none">
            @foreach($lastEntry->images as $imgId)
                @php
                    $media = \App\Models\MediaFile::find($imgId);
                @endphp

                @if($media)
                    <a
                        href="{{ asset($media->path) }}"
                        data-sub-html="
                            <h5 class='mb-1 text-white'>
                                {{ $component->work_component }}
                            </h5>
                            <p class='mb-0 text-light'>
                                Sub Project: {{ $sp['name'] }}
                            </p>
                        "
                    >
                        <img src="{{ asset($media->path) }}" />
                    </a>
                @endif
            @endforeach
        </div>
    @else
        -
    @endif
</td>

            </tr>
            @endforeach
        </x-admin.data-table>
        @endif
    </div>
    @endforeach

</x-admin.card>
{{-- LightGallery CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">

{{-- LightGallery JS --}}
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/fullscreen/lg-fullscreen.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.open-gallery').forEach(button => {
        button.addEventListener('click', function () {

            const galleryId = this.dataset.galleryId;
            const galleryEl = document.getElementById(galleryId);

            if (!galleryEl) return;

            // Destroy previous instance if exists
            if (galleryEl.lgInstance) {
                galleryEl.lgInstance.destroy();
            }

            const lgInstance = lightGallery(galleryEl, {
                plugins: [lgZoom, lgThumbnail, lgFullscreen],
                speed: 400,
                zoom: true,
                thumbnail: true,
                fullscreen: true,
                closeOnTap: true,
                escKey: true,
                download: false,
                counter: true,
                showCloseIcon: true,
                mobileSettings: {
                    controls: true,
                    showCloseIcon: true
                }
            });

            galleryEl.lgInstance = lgInstance;

            // Open gallery programmatically
            lgInstance.openGallery();
        });
    });

});
</script>
