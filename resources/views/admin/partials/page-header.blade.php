<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        @if(!empty($icon))
            <i class="{{ $icon }} text-primary me-2"></i>
        @endif
        {{ $title ?? '' }}
    </h4>

    @if(!empty($breadcrumbs) && is_array($breadcrumbs))
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($breadcrumbs as $breadcrumb)
                    @if(isset($breadcrumb['route']))
                        <li class="breadcrumb-item">
                            <a href="{{ route($breadcrumb['route']) }}">
                                {!! $breadcrumb['label'] !!}
                            </a>
                        </li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">
                            {!! $breadcrumb['label'] !!}
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif
</div>
