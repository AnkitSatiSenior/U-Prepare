@props([
    'title' => '',
    'icon' => null,
    'headerClass' => 'bg-light',
])

<div class="card shadow-sm mb-4 border-0">
    <div class="card-header {{ $headerClass }} d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            @if(!empty($icon))
                <i class="{{ $icon }} me-2"></i>
            @endif
            <h5 class="mb-0">{{ $title }}</h5>
        </div>

        {{-- Right side content (badge, actions, etc.) --}}
        <div>
            {{ $headerRight ?? '' }}
        </div>
    </div>

    <div class="card-body">
        {{ $slot }}
    </div>
</div>
