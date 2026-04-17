<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-history text-primary"
            title="Escalation Engine Logs"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Escalation Logs']
            ]"
        />

        {{-- ── ALERTS ─────────────────────────────────────────────────────── --}}
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

        {{-- ── CATEGORY FILTER TABS ───────────────────────────────────────── --}}
        <div class="mb-3">
            <a href="{{ route('admin.escalation_logs.index') }}"
               class="btn btn-sm {{ !$selectedCategory ? 'btn-dark' : 'btn-outline-secondary' }} me-1">
                All Categories
            </a>
            @foreach ($categoryLabels as $key => $label)
                @php
                    $colors = \App\Models\EscalationLog::categoryColors();
                    $color  = $colors[$key] ?? 'secondary';
                @endphp
                <a href="{{ route('admin.escalation_logs.index', ['category' => $key]) }}"
                   class="btn btn-sm {{ $selectedCategory === $key ? 'btn-'.$color : 'btn-outline-'.$color }} me-1">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- ── MAIN TABLE CARD ────────────────────────────────────────────── --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i>
                    System Escalation Records
                    @if($selectedCategory)
                        <span class="badge bg-{{ \App\Models\EscalationLog::categoryColors()[$selectedCategory] ?? 'secondary' }} ms-2">
                            {{ $categoryLabels[$selectedCategory] }}
                        </span>
                    @endif
                </h5>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload();">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>

                    <form action="{{ route('admin.escalation_logs.trigger') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Run the full escalation engine now?\n\nThis will evaluate:\n• Social Safeguard\n• Physical Progress\n• Financial Progress\n• Contract Security\n\nfor ALL projects.')">
                            <i class="fas fa-cogs me-1"></i> Run Engine Now (All Categories)
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="escalation-logs-table"
                    :headers="['ID', 'Category', 'Target Project / Item', 'Compliance', 'Alert Type', 'Level', 'Day Mark', 'Logged At']"
                    :excel="true"
                    :print="true"
                    title="Escalation Logs Export"
                    searchPlaceholder="Search logs..."
                    resourceName="escalation_logs"
                    :pageLength="25">

                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>

                            {{-- Category Badge --}}
                            <td>
                                <span class="badge bg-{{ $log->category_color }}">
                                    @switch($log->escalation_category)
                                        @case('social_safeguard')   <i class="fas fa-leaf me-1"></i>     @break
                                        @case('physical_progress')  <i class="fas fa-hard-hat me-1"></i> @break
                                        @case('financial_progress') <i class="fas fa-rupee-sign me-1"></i>   @break
                                        @case('contract_security')  <i class="fas fa-shield-alt me-1"></i>@break
                                    @endswitch
                                    {{ $log->category_label }}
                                </span>
                            </td>

                            {{-- Escalatable entity name --}}
                            <td class="fw-bold text-dark">
                                {{ $log->escalatable->name
                                    ?? ($log->escalatable->type?->name ?? 'Unknown Entity') }}
                                <br>
                                <small class="text-muted">
                                    {{ class_basename($log->escalatable_type) }} #{{ $log->escalatable_id }}
                                </small>
                            </td>

                            {{-- Compliance (social only) --}}
                            <td>
                                @if($log->compliance)
                                    {{ $log->compliance->name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Alert type --}}
                            <td>
                                @if(strtolower($log->type) === 'alert')
                                    <span class="badge bg-danger"><i class="fas fa-bell me-1"></i> Alert</span>
                                @elseif(strtolower($log->type) === 'reminder')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Reminder</span>
                                @else
                                    <span class="badge bg-info text-dark">{{ ucfirst($log->type) }}</span>
                                @endif
                            </td>

                            {{-- Level --}}
                            <td>
                                <span class="badge bg-secondary">Level {{ $log->level }}</span>
                            </td>

                            {{-- Day mark --}}
                            <td>
                                <span class="fw-semibold text-dark">Day {{ $log->day_mark }}</span>
                            </td>

                            {{-- Timestamp --}}
                            <td class="text-muted small">
                                {{ $log->created_at?->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                No escalation logs found{{ $selectedCategory ? ' for this category' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>