<x-app-layout>
    <div class="container-fluid">

        <x-admin.breadcrumb-header
            icon="fas fa-shield-alt text-success"
            title="Safeguard Entries"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Safeguard'],
                ['label' => 'Already Defined Entries']
            ]"
        />

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

        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i> Filter Entries
                </h5>
                <a href="{{ route('admin.already-define-safeguards.index') }}" class="btn btn-sm btn-light text-primary">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
            </div>
            <div class="card-body bg-light">
                <form action="{{ route('admin.already-define-safeguards.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Compliance</label>
                            <select name="safeguard_compliance_id" id="filter_compliance_id" class="form-select">
                                <option value="">-- All Compliances --</option>
                                @foreach ($safeguardCompliances as $item)
                                    <option value="{{ $item->id }}" 
                                        data-phases='@json($item->contraction_phases)'
                                        {{ request('safeguard_compliance_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Phase</label>
                            <select name="contraction_phase_id" id="filter_phase_id" class="form-select">
                                <option value="">-- All Phases --</option>
                                @foreach ($contractionPhases as $phase)
                                    <option value="{{ $phase->id }}" 
                                        {{ request('contraction_phase_id') == $phase->id ? 'selected' : '' }}>
                                        {{ $phase->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- All Categories --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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
                        <div class="col-md-4">
                            <label class="form-label">Compliance <span class="text-danger">*</span></label>
                            <select name="safeguard_compliance_id" id="import_compliance_id" class="form-select" required>
                                <option value="">-- Select Compliance --</option>
                                @foreach ($safeguardCompliances as $item)
                                    <option value="{{ $item->id }}" data-phases='@json($item->contraction_phases)'>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Construction Phase <span class="text-danger">*</span></label>
                            <select name="contraction_phase_id" id="import_phase_id" class="form-select" required>
                                <option value="">-- Select Phase --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Category (Optional)</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

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

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Imported Safeguard Entries
                </h5>
                <span class="badge bg-secondary">Total Records: {{ count($entries) }}</span>
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="already-define-entries"
                    :headers="['ID', 'Compliance', 'Phase', 'Category', 'SL No', 'Description', 'Validity', 'Major Head', 'Count', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Safeguard Entries Export"
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
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.already-define-safeguards.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            /**
             * Generic function to handle Compliance -> Phase dependency
             * @param {string} sourceId - ID of the Compliance dropdown
             * @param {string} targetId - ID of the Phase dropdown
             */
            function attachPhaseLoader(sourceId, targetId) {
                const source = document.getElementById(sourceId);
                const target = document.getElementById(targetId);

                if (source && target) {
                    source.addEventListener('change', function() {
                        let phases = [];
                        
                        // Parse phases from the selected option's data attribute
                        if (this.selectedOptions.length > 0 && this.selectedOptions[0].dataset.phases) {
                            phases = JSON.parse(this.selectedOptions[0].dataset.phases);
                        }

                        // Reset Target Dropdown
                        let defaultText = targetId.includes('filter') ? '-- All Phases --' : '-- Select Phase --';
                        target.innerHTML = `<option value="">${defaultText}</option>`;

                        // Populate new options
                        phases.forEach(phase => {
                            target.innerHTML += `<option value="${phase.id}">${phase.name}</option>`;
                        });
                    });
                }
            }

            // 1. Attach to Filter Form
            attachPhaseLoader('filter_compliance_id', 'filter_phase_id');

            // 2. Attach to Import Form
            attachPhaseLoader('import_compliance_id', 'import_phase_id');
        });
    </script>
</x-app-layout>