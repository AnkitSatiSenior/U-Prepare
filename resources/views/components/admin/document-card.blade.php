@props(['doc'])

<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
    <div class="card h-100 shadow-sm border-0 transition-hover">
        
        {{-- 1. Preview Area (Image or Icon) --}}
        <div class="card-img-top position-relative d-flex align-items-center justify-content-center bg-light border-bottom" 
             style="height: 180px; overflow: hidden;">
            
            @if ($doc['type'] === 'image')
                {{-- Image Preview --}}
                <img src="{{ $doc['url'] }}" 
                     alt="{{ $doc['name'] }}" 
                     class="w-100 h-100" 
                     style="object-fit: cover;">
            @else
                {{-- PDF / File Icon --}}
                <div class="text-center p-3">
                    <i class="fas fa-file-pdf fa-4x text-danger mb-2 opacity-75"></i>
                    <span class="d-block small text-muted text-uppercase fw-bold">PDF Document</span>
                </div>
            @endif

            {{-- Hover Overlay (Optional Visual Touch) --}}
            <a href="{{ $doc['url'] }}" target="_blank" class="stretched-link"></a>
        </div>

        {{-- 2. Card Body --}}
        <div class="card-body d-flex flex-column p-3">
            {{-- Title --}}
            <h6 class="card-title text-dark fw-bold text-truncate mb-2" title="{{ $doc['name'] }}">
                {{ $doc['name'] }}
            </h6>

            {{-- Date --}}
            @if (!empty($doc['date']))
                <div class="small text-muted mb-3">
                    <i class="far fa-calendar-alt text-primary me-1"></i> 
                    {{ \Carbon\Carbon::parse($doc['date'])->format('d M, Y') }}
                </div>
            @endif

            {{-- 3. View Button --}}
            <div class="mt-auto">
                <a href="{{ $doc['url'] }}" target="_blank" class="btn btn-outline-primary w-100 btn-sm fw-bold">
                    <i class="fas fa-eye me-1"></i> View File
                </a>
            </div>
        </div>

    </div>
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>