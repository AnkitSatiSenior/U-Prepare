@props([
    'id' => 'dropdownMenu',
    'label' => 'Select',
    'items' => [], // array of ['value' => '', 'label' => '']
    'selected' => null,
    'name' => 'dropdown',
    'required' => false,
    'class' => '',
    'placeholder' => 'Select an option',
    'allowClear' => true,
    'searchable' => true,
])

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.select2-component').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                placeholder: $(this).data('placeholder') || 'Select an option',
                allowClear: $(this).data('allow-clear') === 'true',
                minimumResultsForSearch: $(this).data('searchable') === 'true' ? 0 : -1,
                width: '100%',
                dropdownParent: $(this).parent()
            });
        });
    });
</script>

<div class="mb-3">
    <p for="{{ $id }}" class="form-label fw-semibold h3">
        {{ $label }}{{ $required ? ' *' : '' }}
    </p>

    <select name="{{ $name }}" id="{{ $id }}"
        class="select2-component {{ $class }} @error($name) is-invalid @enderror"
        data-placeholder="{{ $placeholder }}" data-allow-clear="{{ $allowClear ? 'true' : 'false' }}"
        data-searchable="{{ $searchable ? 'true' : 'false' }}" {{ $required ? 'required' : '' }} {{ $attributes }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($items as $item)
            <option value="{{ $item['value'] }}" {{ (string) $selected === (string) $item['value'] ? 'selected' : '' }}
                {{ isset($item['disabled']) && $item['disabled'] ? 'disabled' : '' }}>
                {{ $item['label'] }}
            </option>
        @endforeach
    </select>

</div>
