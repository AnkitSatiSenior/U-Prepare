@props(['value' => null])

<x-label {{ $attributes }} :value="$value">
    {{ $slot }}
</x-label>

