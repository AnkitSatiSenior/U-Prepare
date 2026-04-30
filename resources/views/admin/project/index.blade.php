<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header 
            icon="fas fa-project-diagram text-info" 
            title="EAP Projects" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['route' => 'admin.project.index', 'label' => 'Projects'], 
                ['label' => 'Project List']
            ]" /> 

        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold">External Aided Projects (EAP)</h5>
                <a href="{{ route('admin.project.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add Project
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table id="projects-table" :headers="['ID', 'Name (Short)', 'Agency', 'Budget (INR Cr)', 'Status', 'Actions']" :excel="true" :print="true">
                    @foreach ($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>
                                <strong>{{ $project->name }}</strong><br>
                                <small class="text-muted">{{ $project->project_short_name }}</small>
                            </td>
                            <td>{{ $project->fundingAgency->name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($project->budget / 10000000, 2) }}</td>
                            <td>
                                <span class="badge {{ $project->is_dli_based ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $project->is_dli_based ? 'DLI' : 'Non-DLI' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.project.edit', $project) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.project.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete project?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>