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

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> System Escalation Records
                </h5>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload();">
                        <i class="fas fa-sync-alt me-1"></i> Refresh View
                    </button>
                    
                    <form action="{{ route('admin.escalation_logs.trigger') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Run the escalation engine now? This will evaluate all projects.')">
                            <i class="fas fa-cogs me-1"></i> Run Engine Now
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <x-admin.data-table id="escalation-logs-table" 
                    :headers="['ID', 'Target Project', 'Compliance Type', 'Escalation Type', 'Target Level', 'Day Mark', 'Logged At']" 
                    :excel="true" 
                    :print="true"
                    title="Escalation Logs Export" 
                    searchPlaceholder="Search logs..." 
                    resourceName="escalation_logs"
                    :pageLength="25">
                    
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            
                            {{-- Resolves the Polymorphic relationship dynamically --}}
                            <td class="fw-bold text-dark">
                                {{ $log->escalatable->name ?? 'Unknown Entity' }}
                                <br>
                                <small class="text-muted">ID: {{ $log->escalatable_id }}</small>
                            </td>
                            
                            <td>{{ $log->compliance->name ?? 'N/A' }}</td>
                            
                            {{-- Visual formatting for Type --}}
                            <td>
                                @if(strtolower($log->type) === 'alert')
                                    <span class="badge bg-danger"><i class="fas fa-bell me-1"></i> Alert</span>
                                @elseif(strtolower($log->type) === 'reminder')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Reminder</span>
                                @else
                                    <span class="badge bg-info text-dark">{{ ucfirst($log->type) }}</span>
                                @endif
                            </td>

                            {{-- Target Hierarchy Level --}}
                            <td>
                                <span class="badge border border-primary text-primary bg-light">
                                    <i class="fas fa-layer-group me-1"></i> Level {{ $log->level }}
                                </span>
                            </td>

                            <td>Day {{ $log->day_mark }}</td>

                            <td>
                                <span class="text-muted">
                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>