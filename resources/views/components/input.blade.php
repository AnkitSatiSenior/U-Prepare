@props(['disabled' => false, 'type' => 'text'])

<input 
    type="{{ $type }}" 
    {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge(['class' => 'form-control' . ($errors->has($attributes->get('name')) ? ' is-invalid' : '')]) !!}
>