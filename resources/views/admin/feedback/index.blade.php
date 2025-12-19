{{-- resources/views/admin/feedback/index.blade.php --}}
<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-comments text-primary"
            title="Feedback Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Feedback']
            ]"
        />

        <!-- Alerts -->
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

        <!-- Feedback Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Feedback List
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="feedback-table"
                    :headers="['ID','Name','Email','Type','Subject','Date','Actions']"
                    :excel="true"
                    :print="true"
                    title="Feedback Export"
                    searchPlaceholder="Search feedback..."
                    resourceName="feedback"
                    :pageLength="10"
                >
                    @foreach ($feedbacks as $fb)
                        <tr>
                            <td>{{ $fb->id }}</td>
                            <td>{{ $fb->name }}</td>
                            <td>{{ $fb->email }}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($fb->type) }}
                                </span>
                            </td>
                            <td>{{ $fb->subject ?? '—' }}</td>
                            <td>{{ $fb->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.feedback.show', $fb) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>

                                    <form action="{{ route('admin.feedback.destroy', $fb) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this feedback?')">
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
</x-app-layout>
