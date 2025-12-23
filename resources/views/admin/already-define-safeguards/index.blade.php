<x-app-layout>
    <div class="container-fluid">

        <!-- ========================= BREADCRUMB ========================= -->
        <x-admin.breadcrumb-header
            icon="fas fa-shield-alt text-success"
            title="Safeguard Entries"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Safeguard'],
                ['label' => 'Already Defined Entries']
            ]"
        />

        <!-- ========================= ALERTS ========================= -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        @if ($errors->any())
            <x-alert type="danger">
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <!-- ========================= IMPORT FORM ========================= -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-import me-2"></i> Import Safeguard Entries
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.already-define-safeguards.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">

                        <!-- Compliance -->
                        <div class="col-md-4">
                            <label class="form-label">Compliance <span class="text-danger">*</span></label>
                            <select name="safeguard_compliance_id" id="safeguard_compliance_id" class="form-select" required>
                                <option value="">-- Select Compliance --</option>
                                @foreach ($safeguardCompliances as $item)
                                    <option value="{{ $item->id }}" data-phases='@json($item->contraction_phases)'>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Phase -->
                        <div class="col-md-4">
                            <label class="form-label">Construction Phase <span class="text-danger">*</span></label>
                            <select name="contraction_phase_id" id="contraction_phase_id" class="form-select" required>
                                <option value="">-- Select Phase --</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-md-4">
                            <label class="form-label">Category (Optional)</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- File Upload -->
                        <div class="col-md-4">
                            <label class="form-label">Excel File <span class="text-danger">*</span></label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-success">
                            <i class="fas fa-upload me-1"></i> Upload
                        </button>
                        <a href="/safeguard_entries_demo.xlsx" class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-1"></i> Download Template
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================= TABLE LIST ========================= -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Imported Safeguard Entries
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="already-define-entries"
                    :headers="[
                        'ID',
                        'Compliance',
                        'Phase',
                        'Category',
                        'SL No',
                        'Description',
                        'Validity',
                        'Major Head',
                        'Count',
                        'Actions'
                    ]"
                    :excel="true"
                    :print="true"
                    title="Safeguard Entries Export"
                    searchPlaceholder="Search entries..."
                >
                    @foreach ($entries as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->safeguardCompliance->name ?? '—' }}</td>
                            <td>{{ $item->contractionPhase->name ?? '—' }}</td>
                            <td>{{ $item->category->name ?? '—' }}</td>
                            <td>{{ $item->sl_no }}</td>
                            <td>{{ $item->item_description }}</td>
                            <td>
                                <span class="badge {{ $item->is_validity ? 'bg-success' : 'bg-danger' }}">
                                    {{ $item->is_validity ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $item->is_major_head ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ $item->is_major_head ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>{{ $item->total_entries ?? 1 }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.already-define-safeguards.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.already-define-safeguards.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>

    </div>

    <!-- ========================= PHASE AUTO-LOAD JS ========================= -->
    <script>
        document.getElementById('safeguard_compliance_id').addEventListener('change', function() {
            let phases = JSON.parse(this.selectedOptions[0].dataset.phases || "[]");
            let dropdown = document.getElementById('contraction_phase_id');
            dropdown.innerHTML = `<option value="">-- Select Phase --</option>`;
            phases.forEach(phase => {
                dropdown.innerHTML += `<option value="${phase.id}">${phase.name}</option>`;
            });
        });
    </script>
</x-app-layout>
