{{-- resources/views/admin/feedback/show.blade.php --}}
<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-comment-dots text-primary"
            title="Feedback Details"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['route' => 'admin.feedback.index', 'label' => 'Feedback'],
                ['label' => 'View']
            ]"
        />

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-info-circle me-2"></i> Feedback Info
                </h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $feedback->name }}</dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $feedback->email }}</dd>

                    <dt class="col-sm-3">Type</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-info text-dark">{{ ucfirst($feedback->type) }}</span>
                    </dd>

                    <dt class="col-sm-3">Subject</dt>
                    <dd class="col-sm-9">{{ $feedback->subject ?? '—' }}</dd>

                    <dt class="col-sm-3">Message</dt>
                    <dd class="col-sm-9">{{ $feedback->message }}</dd>

                    <dt class="col-sm-3">IP Address</dt>
                    <dd class="col-sm-9">{{ $feedback->ip_address ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Submitted At</dt>
                    <dd class="col-sm-9">{{ $feedback->created_at->format('d M Y, h:i A') }}</dd>
                </dl>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.feedback.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
