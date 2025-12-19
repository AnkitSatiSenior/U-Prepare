<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-shield-alt text-primary"
            title="Securities for Contract #{{ $contract->contract_number }}"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Contracts'],
                ['label' => 'Securities'],
            ]" 
        />

        <!-- Flash Messages -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <!-- Security Value Check -->
        <div class="mb-3">
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-balance-scale me-2"></i>
                <div>
                    <strong>Total Contract Value:</strong> ₹ {{ number_format($contract->contract_value, 2) }} <br>
                    <strong>Total Securities Value:</strong> ₹ {{ number_format($totalSecurityValue, 2) }}
                </div>
            </div>

            @if ($needsMoreSecurity)
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ⚠️ Securities value is less than contract value. Please submit more securities.
                </div>
            @else
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    ✅ Securities are sufficient for this contract.
                </div>
            @endif
        </div>

        {{-- ------------------ Active Securities ------------------ --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary"><i class="fas fa-lock-open me-2"></i> Active Securities</h5>

                <div class="d-flex gap-2">
                    @if (canRoute('admin.feedback.index'))
                        <a href="{{ route('admin.feedback.index') }}" 
                           class="btn btn-outline-secondary btn-sm" title="Feedback">
                            <i class="fa fa-comments me-1"></i> Feedback
                        </a>
                    @endif

                    @if (canRoute('admin.contracts.securities.create'))
                        <a href="{{ route('admin.contracts.securities.create', $contract->id) }}"
                           class="btn btn-primary btn-sm" title="Add New Security">
                            <i class="fas fa-plus-circle me-1"></i> Add New Security
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="active-securities-table" 
                    :headers="[
                        'S.No', 'Security Name', 'Form of Security', 'Issuing Authority', 
                        'Amount', 'Issue Start Date', 'Issue End Date', 'Status', 'Document', 'Actions'
                    ]" 
                    :excel="true" 
                    :print="true" 
                    title="Active Securities Export" 
                    searchPlaceholder="Search active securities..." 
                    resourceName="active-securities" 
                    :pageLength="10">

                    @php $serial = 1; @endphp
                    @foreach ($securities as $security)
                        @if (!$security->is_expired)
                            <tr @if($security->is_near_expiry) class="table-warning" @endif>
                                <td>{{ $serial++ }}</td>

                                <td>
                                    <i class="fas fa-shield-alt text-primary me-1"></i>
                                    {{ Str::limit($security->type->name ?? '-', 25) }}
                                </td>

                                <td>
                                    <i class="fas fa-file-alt text-secondary me-1"></i>
                                    {{ Str::limit($security->form->name ?? '-', 20) }}
                                </td>

                                <td>
                                    <i class="fas fa-university text-success me-1"></i>
                                    {{ Str::limit($security->bank_name ?? '-', 25) }}
                                </td>

                                <td>₹ {{ number_format($security->value, 2) }}</td>
                                <td>{{ $security->issue_date ?? '-' }}</td>
                                <td>{{ $security->issued_end_date ?? '-' }}</td>

                                <td>
                                    @if($security->is_near_expiry)
                                        <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> Expiring Soon</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Active</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($security->file_path)
                                        <a href="{{ Storage::url($security->file_path) }}" target="_blank" title="View Document">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.contracts.securities.edit', [$contract->id, $security->id]) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit Security">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.contracts.securities.destroy', [$contract->id, $security->id]) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this security?')"
                                                title="Delete Security">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                </x-admin.data-table>
            </div>
        </div>

        {{-- ------------------ Expired Securities ------------------ --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-danger"><i class="fas fa-history me-2"></i> Expired / Previous Securities</h5>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="expired-securities-table" 
                    :headers="[
                        'S.No', 'Security Name', 'Form of Security', 'Issuing Authority', 
                        'Amount', 'Issue Start Date', 'Issue End Date', 'Document'
                    ]" 
                    :excel="true" 
                    :print="true" 
                    title="Expired Securities Export" 
                    searchPlaceholder="Search expired securities..." 
                    resourceName="expired-securities" 
                    :pageLength="10">

                    @php $serial = 1; @endphp
                    @foreach ($securities as $security)
                        @if ($security->is_expired)
                            <tr class="table-danger" title="This security has expired">
                                <td>{{ $serial++ }}</td>

                                <td>
                                    <i class="fas fa-shield-alt text-primary me-1"></i>
                                    {{ Str::limit($security->type->name ?? '-', 25) }}
                                </td>

                                <td>
                                    <i class="fas fa-file-alt text-secondary me-1"></i>
                                    {{ Str::limit($security->form->name ?? '-', 20) }}
                                </td>

                                <td>
                                    <i class="fas fa-university text-success me-1"></i>
                                    {{ Str::limit($security->bank_name ?? '-', 25) }}
                                </td>

                                <td>₹ {{ number_format($security->value, 2) }}</td>
                                <td>{{ $security->issue_date ?? '-' }}</td>
                                <td>{{ $security->issued_end_date ?? '-' }}</td>

                                <td class="text-center">
                                    @if($security->file_path)
                                        <a href="{{ Storage::url($security->file_path) }}" target="_blank" title="View Document">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>

    </div>
</x-app-layout>
