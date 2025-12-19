@props(['doc'])

<div class="col-md-4 col-sm-6">
    <div class="card shadow-sm border rounded h-100">
        <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center mb-2">
                @if ($doc['type'] === 'image')
                    <i class="fas fa-file-image fa-2x text-success me-2"></i>
                @else
                    <i class="fas fa-file-pdf fa-2x text-danger me-2"></i>
                @endif
                <h6 class="mb-0 fw-semibold">{{ $doc['name'] }}</h6>
            </div>
            @if (!empty($doc['date']))
                <small class="text-muted mb-2">
                    <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($doc['date'])->format('d M Y') }}
                </small>
            @endif
            <div class="mt-auto">
                <a href="{{ $doc['url'] }}" target="_blank" class="btn btn-outline-primary w-100 btn-sm">
                    <i class="fas fa-eye me-1"></i> View
                </a>
            </div>
        </div>
    </div>
</div>
