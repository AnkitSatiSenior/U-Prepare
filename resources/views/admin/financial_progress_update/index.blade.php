<x-app-layout>
    <div class="container py-5">

        <x-admin.breadcrumb-header icon="fas fa-file-invoice-dollar text-primary" :title="'Financial Progress Updates — ' . e(optional($subProject)->name ?? 'No Project Selected')" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i> Dashboard'],
            ['label' => 'Financial Progress Updates'],
        ]" />

        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif

        <!-- Card with Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Financial Progress List
                </h5>
                <a href="{{ route('admin.financial-progress-updates.create', ['sub_package_project_id' => $subProject->id]) }}"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add Progress
                </a>

            </div>
            <div class="card-body">
                <x-admin.data-table :headers="[
                    '#',
                    'Bill Serial No',
                    'No. of Bills',
                    'Finance Amount (₹)',
                    'Media',
                    'Submit Date',
                    'Actions',
                ]" id="financialTable" :excel="true" :print="true"
                    :pageLength="10">

                    @foreach ($financialProgress as $index => $progress)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ is_array($progress->bill_serial_no) ? implode(', ', $progress->bill_serial_no) : $progress->bill_serial_no }}
                            </td>
                            <td>{{ $progress->no_of_bills }}</td>
                            <td>{{ number_format($progress->finance_amount, 2) }}</td>
                            <td>
                                @if (!empty($progress->media_paths))
                                    <button class="btn btn-outline-primary btn-sm view-images-btn"
                                        data-images='@json($progress->media_paths)'>
                                        <i class="fas fa-images me-1"></i> View Media
                                    </button>
                                @else
                                    <span class="text-muted">No files</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($progress->submit_date)->format('d M Y') }}</td>
                            <td>

                                <form action="{{ route('admin.financial-progress-updates.destroy', $progress->id) }}"
                                    method="POST" style="display:inline-block;"
                                    onsubmit="return confirm('Are you sure you want to delete this record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>


            </div>
        </div>
    </div>

    <!-- Modal for media -->
    <div class="modal fade" id="imagesModal" tabindex="-1" aria-labelledby="imagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagesModalLabel">Attached Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="imagesModalBody"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.view-images-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const files = JSON.parse(this.dataset.images);
                    const modalBody = document.getElementById('imagesModalBody');
                    modalBody.innerHTML = '';

                    if (!files.length) {
                        modalBody.innerHTML = '<p class="text-muted">No media available.</p>';
                    } else {
                        let tableHTML = `
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Bill Name</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                        files.forEach((file, index) => {
                            const ext = file.split('.').pop().toLowerCase();
                            const url = "{{ asset('storage') }}/" + file;
                            let type = '';
                            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) type =
                                'Image';
                            else if (ext === 'pdf') type = 'PDF';
                            else type = ext.toUpperCase();

                            tableHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>Bill ${index + 1}</td>
                                <td>${type}</td>
                                <td>
                                    <a href="${url}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        `;
                        });

                        tableHTML += `</tbody></table></div>`;
                        modalBody.innerHTML = tableHTML;
                    }
                    new bootstrap.Modal(document.getElementById('imagesModal')).show();
                });
            });
        });
    </script>
</x-app-layout>
