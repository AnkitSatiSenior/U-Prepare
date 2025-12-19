<x-app-layout>
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <x-admin.breadcrumb-header
        icon="fas fa-file-alt text-primary"
        title="Sub Package Project Test Reports"
        :breadcrumbs="[
            ['route' => 'dashboard','label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Test Reports']
        ]"
    />

    {{-- Alerts --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    {{-- Tests Table --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-primary"><i class="fas fa-list me-2"></i> Tests for "{{ $subProject->name }}"</h5>
        </div>
        <div class="card-body">
            <x-admin.data-table
                id="tests-table"
                :headers="['ID', 'Test Name', 'Status', 'Report Text', 'File', 'Remark', 'Approved By', 'Actions']"
            >
                @foreach($tests as $test)
                    <tr id="row-{{ $test->id }}">
                        <td>{{ $test->id }}</td>
                        <td>{{ $test->test_name }}</td>

                        {{-- Status --}}
                        <td class="status-cell">
                            @if($test->report)
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>

                        {{-- Report Text --}}
                        <td>
                            @if($test->report)
                                {{ $test->report->report }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- File --}}
                        <td>
                            @if($test->report && $test->report->file)
                                <a href="{{ asset('storage/' . $test->report->file) }}" target="_blank">View File</a>
                            @else
                                -
                            @endif
                        </td>

                        {{-- Remark --}}
                        <td>
                            @if($test->report)
                                {{ $test->report->remark ?? '-' }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- Approved By --}}
                        <td>
                            @if($test->report)
                                {{ $test->report->approvedBy?->name ?? 'N/A' }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            @if(!$test->report)
                                <button class="btn btn-sm btn-primary addReportBtn" data-id="{{ $test->id }}">
                                    <i class="fas fa-plus me-1"></i> Add Report
                                </button>
                            @else
                                <span class="text-muted">Submitted</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-admin.data-table>
        </div>
    </div>

</div>

{{-- Modal for Adding Report --}}
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="reportForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="test_id" id="modalTestId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Test Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Report Text</label>
                        <textarea name="report" class="form-control" id="modalReportText" rows="3"></textarea>
                    </div>
                    <div class="mb-2">
                        <label>File</label>
                        <input type="file" name="file" class="form-control" id="modalReportFile" accept=".pdf,.jpg,.png,.docx">
                    </div>
                    <div class="mb-2">
                        <label>Remark</label>
                        <input type="text" name="remark" class="form-control" id="modalReportRemark">
                    </div>
                    <div class="mb-2">
                        <label>Approved By</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                    </div>
                    <div class="text-danger mt-1" id="modalReportError"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Open modal
    $(document).on('click', '.addReportBtn', function() {
        let testId = $(this).data('id');
        $('#modalTestId').val(testId);
        $('#modalReportText').val('');
        $('#modalReportFile').val('');
        $('#modalReportRemark').val('');
        $('#modalReportError').text('');
        $('#reportModal').modal('show');
    });

    // Submit report via AJAX
    $('#reportForm').submit(function(e){
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.sub_package_project_test_reports.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                let row = $('#row-' + res.test_id);
                row.find('.status-cell').html('<span class="badge bg-success">Completed</span>');
                row.find('.report-cell').html(res.report_file ? `<a href="${res.report_file}" target="_blank">View File</a>` : '-');
                row.find('.addReportBtn').replaceWith('<span class="text-muted">Submitted</span>');

                // Update report text, remark, approved by dynamically
                row.find('td:nth-child(4)').text(res.report_text ?? '-');
                row.find('td:nth-child(6)').text(res.remark ?? '-');
                row.find('td:nth-child(7)').text(res.approved_by_name ?? '-');

                $('#reportModal').modal('hide');
                alert(res.success);
            },
            error: function(err){
                let msg = err.responseJSON?.errors?.report?.[0] 
                       || err.responseJSON?.errors?.file?.[0]
                       || err.responseJSON?.errors?.remark?.[0]
                       || err.responseJSON?.error;
                $('#modalReportError').text(msg);
            }
        });
    });
</script>
</x-app-layout>
