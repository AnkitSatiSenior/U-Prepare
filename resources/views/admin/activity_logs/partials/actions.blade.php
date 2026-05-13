<div class="btn-group" role="group" aria-label="Action Buttons">
    {{-- View Details Button --}}
    {{-- BUG FIX: Mapped to the actual 'changes' database column --}}
    <button type="button" 
            class="btn btn-sm btn-info view-log-btn text-white" 
            title="View Data Payload"
            data-model="{{ class_basename($log->model_type) }} ({{ $log->model_id }})"
            data-action="{{ ucfirst($log->action) }}"
            data-payload="{{ is_string($log->changes) ? $log->changes : json_encode($log->changes) }}">
        <i class="fas fa-eye"></i>
    </button>

    {{-- Delete Button --}}
    <form action="{{ route('admin.activity_logs.destroy', $log->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this log?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete Log">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
</div>