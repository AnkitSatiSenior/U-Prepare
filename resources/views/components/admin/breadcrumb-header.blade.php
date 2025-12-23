@props([
    'icon' => '',
    'title' => '',
    'breadcrumbs' => [],
])

<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center row">
            <h4 class="mb-0 h4 col-8">
                <i class="{{ $icon }} me-2"></i> {!! $title !!}
            </h4>

            <nav aria-label="breadcrumb col-4">
                <ol class="breadcrumb mb-0">
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if (isset($breadcrumb['route']))
                            @php
                                // If parameters are provided, pass them to route()
                                $params = $breadcrumb['params'] ?? [];
                            @endphp
                            <li class="breadcrumb-item">
                                <a href="{{ route($breadcrumb['route'], $params) }}">
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
        </div>
    </div>
</div>

{{--  Hot To Call Thsi Fuction  --}}

{{-- 
  <x-admin.breadcrumb-header 
    icon="fas fa-info-circle text-info" 
    title=" EPC Entries" 
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
        ['route' => 'admin.already_define_epc.index', 'label' => 'EPC Entries'], 
        ['label' => 'Details']
    ]" 
/> --}}