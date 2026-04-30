@props(['type' => 'submit'])

<x-button {{ $attributes }} :type="$type" variant="primary">
    {{ $slot }}
</x-button>

