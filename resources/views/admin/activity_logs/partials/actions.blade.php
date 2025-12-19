<button class="btn btn-sm btn-outline-secondary"
    data-bs-toggle="modal"
    data-bs-target="#logModal{{ $log->id }}">
    <i class="fas fa-eye"></i>
</button>

<form action="{{ route('admin.activity_logs.destroy', $log) }}"
      method="POST"
      class="d-inline"
      onsubmit="return confirm('Delete this log?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">
        <i class="fas fa-trash"></i>
    </button>
</form>
