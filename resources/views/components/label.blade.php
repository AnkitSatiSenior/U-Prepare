@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'form-label fw-semibold']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-danger">*</span>
    @endif
</label>