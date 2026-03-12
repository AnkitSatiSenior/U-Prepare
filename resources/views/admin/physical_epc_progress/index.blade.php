<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        @if ($subPackageProjectName)
            <x-admin.breadcrumb-header icon="fas fa-tasks text-primary"
                title="Physical EPC Progress Management Of ( <strong>{{ $subPackageProjectName }}</strong> )"
                :breadcrumbs="[
                    ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                    ['route' => 'admin.physical_epc_progress.index', 'label' => 'Physical EPC Progress'],
                    ['label' => 'Manage Progress Entries'],
                ]" />
        @endif


        <!-- Alerts -->
        @foreach (['success' => 'success', 'error' => 'danger'] as $type => $class)
            @if (session($type))
                <div class="row mb-3">
                    <div class="col-md-12">
                        <x-alert type="{{ $class }}" :message="session($type)" dismissible />
                    </div>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" dismissible>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <a href="{{ route('admin.physical_epc_progress.create', ['sub_package_project_id' => request('sub_package_project_id')]) }}"
                class="btn btn-success shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Add Progress Entry
            </a>

            <button type="button" class="btn btn-danger shadow-sm" id="bulkDeleteBtn" disabled>
                <i class="fas fa-trash-alt me-1"></i> Delete Selected
            </button>
        </div>

        <!-- Progress Entries Table -->
        @if ($progressEntries->isNotEmpty())
            <form id="bulkDeleteForm">@csrf
                <x-admin.data-table id="progressTable" :headers="[
                    '',
                    'SL No.',
                    'Activity',
                    'Stage',
                   
                    'Percent',
                    
                    'Items Done',
                    'Submitted Date',
                    'Images',
                    'Actions',
                ]" :excel="true" :print="true"
                    title="Physical EPC Progress Export" searchPlaceholder="Search progress..."
                    resourceName="progressEntries" :pageLength="10">
                    @foreach ($progressEntries as $entry)
                        <tr>
                            <td>
                                <input type="checkbox" class="entryCheckbox" name="ids[]" value="{{ $entry->id }}"
                                    aria-label="Select entry {{ $entry->id }}">
                            </td>
                            <td>{{ $entry->epcEntryData->sl_no ?? '-' }}</td>
                            <td>{{ $entry->epcEntryData->activity_name ?? '-' }}</td>
                            <td>{{ $entry->epcEntryData->stage_name ?? '-' }}</td>
                            
                            <td>{{ $entry->percent ? $entry->percent . '%' : '-' }}</td>
                            
                            <td>{{ $entry->items ?? '-' }}</td>
                            <td>{{ $entry->progress_submitted_date?->format('d-m-Y') ?? '-' }}</td>
                            <td>
                                
                                @if (!empty($entry->image_urls) && count($entry->image_urls) > 0)
                                    <a href="#" class="btn btn-sm btn-info view-images-btn"
                                        data-images='@json($entry->image_urls)'>
                                        <i class="fas fa-image me-1"></i> View Images
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.physical_epc_progress.edit', $entry->id) }}"
                                        class="btn btn-sm btn-warning" title="Edit Entry">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </form>
        @else
            <div class="alert alert-info">No physical EPC progress records found.</div>
        @endif
    </div>

    <!-- Modal for viewing images -->
    <div class="modal fade" id="imagesModal" tabindex="-1" aria-labelledby="imagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagesModalLabel">Progress Entry Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-wrap gap-3 justify-content-center" id="imagesModalBody">
                    <!-- Images will be injected here -->
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
   <script>
    $(document).ready(function() {
        // Bulk Delete Controls
        const bulkDeleteBtn = $('#bulkDeleteBtn');
        const selectAll = $('#progressTable thead input[type=checkbox]').first();

        // Function to update bulk delete button state
        function updateBtnState() {
            bulkDeleteBtn.prop('disabled', !$('#progressTable tbody .entryCheckbox:checked').length);
        }

        // Select all checkbox toggles all entry checkboxes
        selectAll.on('change', function() {
            $('#progressTable tbody .entryCheckbox').prop('checked', this.checked);
            updateBtnState();
        });

        // Individual checkbox toggles selectAll checkbox
        $('#progressTable tbody').on('change', '.entryCheckbox', function() {
            let allChecked = $('#progressTable tbody .entryCheckbox').length === $(
                '#progressTable tbody .entryCheckbox:checked').length;
            selectAll.prop('checked', allChecked);
            updateBtnState();
        });

        // Bulk delete button click
        bulkDeleteBtn.on('click', function() {
            const ids = $('#progressTable tbody .entryCheckbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (!ids.length) return;

            if (!confirm('Delete selected entries?')) return;

            $.post("{{ route('admin.physical_epc_progress.bulkDestroy') }}", {
                ids,
                _method: 'DELETE',
                _token: "{{ csrf_token() }}"
            }).done(() => location.reload());
        });

        // 🏗️ ARCHITECTURE FIX: Securely inject the base S3 bucket URL from Laravel.
        // rtrim prevents double-slash corruption when concatenating later.
        const s3BaseUrl = "{{ rtrim(\Illuminate\Support\Facades\Storage::disk('s3')->url(''), '/') }}";

        // View Images button click - show modal with images
        $('#progressTable tbody').on('click', '.view-images-btn', function(e) {
            e.preventDefault();
            const images = $(this).data('images');
            const modalBody = $('#imagesModalBody');
            modalBody.empty();

            if (!images || images.length === 0) {
                modalBody.append('<p class="text-muted"><i class="fas fa-info-circle me-1"></i> No images available.</p>');
            } else {
                images.forEach(img => {
                    // 🏗️ ARCHITECTURE FIX: Strip leading slashes from the image path and build the strict S3 URL.
                    const cleanImg = img.replace(/^\/+/, '');
                    const url = s3BaseUrl + '/' + cleanImg;

                    modalBody.append(`
                        <a href="${url}" target="_blank" rel="noopener noreferrer" class="d-inline-block m-1">
                            <img src="${url}" 
                                 alt="Progress Image" 
                                 class="rounded shadow-sm border" 
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 loading="lazy">
                        </a>
                    `);
                });
            }

            var modal = new bootstrap.Modal(document.getElementById('imagesModal'));
            modal.show();
        });
    });
</script>
</x-app-layout>
