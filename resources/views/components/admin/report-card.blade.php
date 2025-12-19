<div class="col-md-3 mb-3">
    @if($route)
        <a href="{{ $route }}" class="text-decoration-none">
    @endif

    <div class="card shadow-sm border-0 bg-{{ $color }} text-white h-100">
        <div class="card-body d-flex flex-column justify-content-center text-center">
            <h6>{{ $title }}</h6>
            <h3>{{ $count }}</h3>
        </div>
    </div>

    @if($route)
        </a>
    @endif
</div>
