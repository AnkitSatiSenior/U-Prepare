<x-app-layout>
    <style>
        .h4 {
            font-size: 18px !important;
            font-weight: 550;
        }

        .timeline h2 {
            font-size: 1.25rem;
        }

        .timeline p {
            font-size: 1rem;
        }

        .bsb-timeline-1 {
            --bsb-tl-color: #cfe3ff;
            --bsb-tl-circle-size: 18px;
            --bsb-tl-circle-color: #0d6ef6;
            --bsb-tl-circle-offset: 9px;
        }

        .bsb-timeline-1 .timeline {
            margin: 0;
            padding: 0;
            position: relative;
            list-style: none;
        }

        .bsb-timeline-1 .timeline::after {
            top: 0;
            left: 0;
            width: 2px;
            bottom: 0;
            content: "";
            position: absolute;
            margin-left: -1px;
            background-color: var(--bsb-tl-color);
        }

        .bsb-timeline-1 .timeline>.timeline-item {
            position: relative;
        }

        .bsb-timeline-1 .timeline>.timeline-item::before {
            top: 0;
            left: calc(var(--bsb-tl-circle-offset)*-2);
            width: var(--bsb-tl-circle-size);
            height: var(--bsb-tl-circle-size);
            content: "";
            position: absolute;
            border-radius: 50%;
            background-color: var(--bsb-tl-circle-color);
        }

        .bsb-timeline-1 .timeline>.timeline-item .timeline-content {
            padding: 0 0 2rem 2.5rem;
        }
    </style>

    <div class="container-fluid">
        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header icon="fas fa-file-alt text-primary" title="Grievance Details" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['route' => 'admin.grievances.index', 'label' => 'Grievances'],
            ['label' => 'Details'],
        ]" />

        {{-- Applicant Details --}}
        <section class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-header bg-light fw-bold d-flex align-items-center fs-5"> <i
                    class="fas fa-user text-primary me-2"></i> Applicant Details </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1 h3">Name</h6>
                        <p class="fw-semibold fs-6 mb-0 h5">{{ $grievance->full_name ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1 h3">Mobile</h6>
                        <p class="fw-semibold fs-6 mb-0 h5">{{ $grievance->mobile ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1 h3">Email</h6>
                        <p class="fw-semibold fs-6 mb-0 h5">{{ $grievance->email ?? '—' }}</p>
                    </div>

                    <!-- Long Text Fields -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1 h3">Description</h6>
                        <div class="border rounded-3 bg-light p-3"
                            style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                            <p class="mb-0 fs-6 lh-base text-dark">{{ $grievance->description ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-1 h3">Detail of Complaint</h6>
                        <div class="border rounded-3 bg-light p-3"
                            style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                            <p class="mb-0 fs-6 lh-base text-dark">{{ $grievance->detail_of_complaint ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        {{-- Grievance Info --}}
        <section class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">
                <i class="fas fa-info-circle text-primary me-2"></i> Grievance Information
            </div>
            <div class="card-body">
                <h6 class="text-muted">Related To</h6>
                <h5>{{ $grievance->grievance_related_to ?? '—' }}</h5>

                <h6 class="mt-3 text-muted">Nature of Complaint</h6>
                <p class="h4">{{ $grievance->nature_of_complaint ?? '—' }}</p>


                <h6 class="mt-3 text-muted">Sub Package Name</h6>
                <p>{{ $grievance->project ?? '—' }}</p>

                <h6 class="mt-3 text-muted">Status</h6>
                <span
                    class="badge text-white h5 bg-{{ $grievance->status == 'resolved' ? 'success' : ($grievance->status == 'pending' ? 'warning' : ($grievance->status == 'rejected' ? 'danger' : 'secondary')) }}">
                    {{ ucfirst($grievance->status) ?? '—' }}
                </span>

                {{-- Status Update Form (disabled if resolved/rejected) --}}
                @if (!in_array($grievance->status, ['resolved', 'rejected']))
                    <form class="ajax-form-status mt-3" data-method="POST"
                        data-action="{{ route('admin.grievances.updateStatus', $grievance->id) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-4">
                                <select name="status" class="form-control" required>
                                    <option value="">Change Status</option>
                                    <option value="pending" {{ $grievance->status == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="in-progress"
                                        {{ $grievance->status == 'in-progress' ? 'selected' : '' }}>
                                        In Progress</option>
                                    <option value="resolved" {{ $grievance->status == 'resolved' ? 'selected' : '' }}>
                                        Resolved</option>
                                    <option value="rejected" {{ $grievance->status == 'rejected' ? 'selected' : '' }}>
                                        Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="remark" class="form-control"
                                    placeholder="Remark (optional)">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i>
                                    Update</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </section>

        {{-- Assignments --}}
        <section class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">
                <i class="fas fa-users text-primary me-2"></i> Assignments
            </div>
            <div class="card-body">
                <form class="ajax-form-assign mb-3" data-method="POST"
                    data-action="{{ route('admin.grievances.assignments.store', $grievance->id) }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-5">
                            <select name="assigned_to" class="form-control" required>
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="department" class="form-control" placeholder="Department"
                                required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus"></i>
                                Assign</button>
                        </div>
                    </div>
                </form>

                <ul class="list-group">
                    @forelse($grievance->assignments as $assign)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong>{{ $assign->assignedUser->name ?? '—' }}</strong>
                                ({{ $assign->department ?? '—' }})
                                <small class="text-muted">by {{ $assign->assignedByUser->name ?? 'System' }}</small>
                            </span>
                            <button class="btn btn-danger btn-sm ajax-delete"
                                data-url="{{ route('admin.grievances.assignments.destroy', $assign->id) }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </li>
                    @empty
                        <p class="text-muted">No assignments yet.</p>
                    @endforelse
                </ul>
            </div>
        </section>

        {{-- Preliminary & Final Action --}}
        @php
            $prelimLog = $grievance->logs->where('type', 'preliminary')->first();
            $finalLog = $grievance->logs->where('type', 'final')->first();
        @endphp
        <div class="row">
            {{-- Preliminary --}}
            <div class="col-md-6">
                <section class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Preliminary Action Taken</div>
                    <div class="card-body">
                        @if ($prelimLog)
                            <h6 class="text-muted">{{ $prelimLog->created_at->format('d M Y, h:i A') }}</h6>
                            <p><strong>Remark:</strong> {{ $prelimLog->remark ?? '—' }}</p>
                            @if ($prelimLog->document)
                                <p><a href="{{ asset('storage/' . $prelimLog->document) }}" target="_blank">View
                                        Document</a></p>
                            @endif
                            <p><strong>By:</strong> {{ $prelimLog->user->name ?? 'System' }}</p>
                        @else
                            <p class="text-muted">No preliminary action yet.</p>
                            <div class="text-center">
                                <button class="btn btn-primary btn-patr" data-bs-toggle="modal"
                                    data-bs-target="#actionModal">Submit</button>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            {{-- Final --}}
            <div class="col-md-6">
                <section class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Final Action Taken</div>
                    <div class="card-body">
                        @if ($finalLog)
                            <h6 class="text-muted">{{ $finalLog->created_at->format('d M Y, h:i A') }}</h6>
                            <p><strong>Remark:</strong> {{ $finalLog->remark ?? '—' }}</p>
                            @if ($finalLog->document)
                                <p><a href="{{ asset('storage/' . $finalLog->document) }}" target="_blank">View
                                        Document</a></p>
                            @endif
                            <p><strong>By:</strong> {{ $finalLog->user->name ?? 'System' }}</p>
                        @elseif($prelimLog)
                            <p class="text-muted">No final action yet.</p>
                            <div class="text-center">
                                <button class="btn btn-primary btn-fatr" data-bs-toggle="modal"
                                    data-bs-target="#actionModal">Submit</button>
                            </div>
                        @else
                            <p class="text-muted">Please complete Preliminary Action first.</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>

        {{-- Logs Timeline --}}
        <section class="card shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="fas fa-history text-primary me-2"></i> Grievance Logs
            </div>
            <div class="card-body">
                {{-- Add Log Form --}}
                @if (canRoute('admin.grievances.logs.store'))
                    <form class="ajax-form-log mb-3" data-method="POST"
                        data-action="{{ route('admin.grievances.logs.store', $grievance->id) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="title" class="form-control" placeholder="Log Title"
                                    required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="remark" class="form-control" placeholder="Remark"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <input type="file" name="document" class="form-control">
                            </div>
                            <div class="col-md-12 mt-2 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Log
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                {{-- Timeline --}}
                <div class="bsb-timeline-1 py-4">
                    <ul class="timeline">
                        @forelse($grievance->logs as $log)
                            <li class="timeline-item">
                                <div class="timeline-content">
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted mb-1">
                                                        {{ $log->created_at->format('d M Y, h:i A') }}</h6>
                                                    <h5 class="fw-bold mb-1">
                                                        {{ $log->title ?? ucfirst($log->type) . ' Action' }}</h5>
                                                    <p class="mb-1"><strong>Remark:</strong>
                                                        {{ $log->remark ?? '—' }}</p>
                                                    <p class="mb-1"><strong>By:</strong>
                                                        {{ $log->user->name ?? 'System' }}</p>
                                                    @if ($log->document)
                                                        <p class="mb-0">
                                                            <a href="{{ asset('storage/' . $log->document) }}"
                                                                target="_blank" class="text-primary">📄 View
                                                                Document</a>
                                                        </p>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if (canRoute('admin.grievances.logs.update'))
                                                        <button class="btn btn-sm btn-warning me-1 btn-edit-log"
                                                            data-id="{{ $log->id }}"
                                                            data-title="{{ $log->title }}"
                                                            data-remark="{{ $log->remark }}"
                                                            data-document="{{ $log->document ? asset('storage/' . $log->document) : '' }}">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                    @endif

                                                    @if (canRoute('admin.grievances.logs.destroy'))
                                                        <button class="btn btn-sm btn-danger ajax-delete"
                                                            data-url="{{ route('admin.grievances.logs.destroy', $log->id) }}">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-muted">No logs available.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

        </section>

        {{-- ==================== Update Modal ==================== --}}
        <div class="modal fade" id="editLogModal" tabindex="-1" aria-labelledby="editLogModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="updateLogForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="editLogModalLabel"><i class="fas fa-edit me-2"></i> Update
                                Log</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="log_id" name="log_id">

                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" id="edit_title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Remark</label>
                                <textarea name="remark" id="edit_remark" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Replace Document (optional)</label>
                                <input type="file" name="document" class="form-control">
                                <div class="mt-1">
                                    <a href="#" id="current_document_link" target="_blank"
                                        class="small text-primary d-none">View Current Document</a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Created At</label>
                                <input type="datetime-local" name="created_at" id="edit_created_at"
                                    class="form-control">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Log</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== JavaScript ==================== --}}

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Open edit modal with data
                $(document).on('click', '.btn-edit-log', function() {
                    const id = $(this).data('id');
                    const title = $(this).data('title');
                    const remark = $(this).data('remark');
                    const documentLink = $(this).data('document');
                    const createdAt = $(this).data('created_at'); // Pass this from backend
                    const modal = $('#editLogModal');

                    $('#edit_title').val(title);
                    $('#edit_remark').val(remark);
                    $('#log_id').val(id);
                    $('#updateLogForm').attr('action', `/admin/grievance-logs/${id}`);

                    // Populate current document link
                    if (documentLink) {
                        $('#current_document_link').attr('href', documentLink).removeClass('d-none');
                    } else {
                        $('#current_document_link').addClass('d-none');
                    }

                    // Set created_at field (convert to proper format for datetime-local)
                    if (createdAt) {
                        const dt = new Date(createdAt);
                        const formatted = dt.toISOString().slice(0, 16); // "YYYY-MM-DDTHH:MM"
                        $('#edit_created_at').val(formatted);
                    } else {
                        $('#edit_created_at').val('');
                    }

                    modal.modal('show');
                });


                // Handle update form via AJAX
                $('#updateLogForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const url = form.attr('action');
                    const formData = new FormData(this);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.success) {
                                $('#editLogModal').modal('hide');
                                alert(res.message);
                                location.reload();
                            } else {
                                alert(res.message || 'Failed to update log');
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            alert('Something went wrong while updating.');
                        }
                    });
                });

            });
        </script>


    </div>

    {{-- Action Modal --}}
    <div class="modal fade" id="actionModal">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content ajax-form-action" data-method="POST" data-action="#"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="grievance_id" value="{{ $grievance->id }}">
                <input type="hidden" name="type" value="">
                <input type="hidden" name="title" value="">
                <div class="modal-header">
                    <h5 class="modal-title">Action Taken Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Comment</label>
                        <textarea name="remark" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Attach Document</label>
                        <input type="file" name="document" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(function() {
            // Cache modal and form inputs
            const $modal = $('#actionModal');
            const $type = $modal.find('input[name="type"]');
            const $title = $modal.find('input[name="title"]');

            /**
             * 🟢 Open Modal for Preliminary or Final Action Taken Report
             */
            $('.btn-patr, .btn-fatr').on('click', function() {
                const isFinal = $(this).hasClass('btn-fatr');

                $type.val(isFinal ? 'final' : 'preliminary');
                $title.val(isFinal ? 'Final Action' : 'Preliminary Action');
                $modal.find('.modal-title').text(isFinal ? 'Final Action Taken Report' :
                    'Preliminary Action Taken Report');
                $modal.find('form').attr('data-action',
                    "{{ route('admin.grievances.logs.store', $grievance->id) }}");
            });

            /**
             * ⚙️ Handle AJAX Form Submission (generic for all ajax forms)
             */
            function handleAjaxForm(form) {
                const action = form.data('action');
                const method = form.data('method') || 'POST';
                const formData = new FormData(form[0]);
                const btn = form.find('[type="submit"]');

                btn.prop('disabled', true);

                $.ajax({
                    url: action,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message ?? "Saved successfully");
                        } else {
                            toastr.warning(res.message ?? "Something went wrong!");
                        }
                        setTimeout(() => window.location.reload(), 600);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || "Unexpected error");
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            }

            /**
             * 📨 Bind AJAX Form Submission
             */
            $(document).on('submit', '.ajax-form-status, .ajax-form-assign, .ajax-form-log, .ajax-form-action',
                function(e) {
                    e.preventDefault();
                    handleAjaxForm($(this));
                });

            /**
             * 🗑️ AJAX Delete Handler
             */
            $(document).on('click', '.ajax-delete', function(e) {
                e.preventDefault();
                if (!confirm("Are you sure to delete?")) return;

                const url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message ?? "Deleted successfully");
                            setTimeout(() => window.location.reload(), 600);
                        } else {
                            toastr.warning(res.message ?? "Delete failed");
                        }
                    },
                    error: function() {
                        toastr.error("Delete failed");
                    }
                });
            });
        });
    </script>

</x-app-layout>
