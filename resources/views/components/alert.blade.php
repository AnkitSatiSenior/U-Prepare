@props([
    'type' => 'success', // success, danger, warning, info
    'dismissible' => true,
    'message' => '',
])

@php
    $alertClasses = [
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];
    
    $iconClasses = [
        'success' => 'fa-check-circle',
        'danger' => 'fa-exclamation-triangle',
        'warning' => 'fa-exclamation-circle',
        'info' => 'fa-info-circle',
    ];
@endphp

<div class="alert {{ $alertClasses[$type] }} alert-dismissible fade show" role="alert">
    <i class="fas {{ $iconClasses[$type] }} me-2"></i>
    {{ $message ?? $slot }}
    
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>