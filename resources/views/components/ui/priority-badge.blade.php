@props(['priority'])

@php
    $value = $priority instanceof \BackedEnum ? $priority->value : $priority;
    $label = $priority instanceof \BackedEnum ? $priority->label() : ucfirst($value);
    $colors = [
        'low' => 'slate',
        'medium' => 'blue',
        'high' => 'red',
    ];
@endphp

<x-ui.badge :color="$colors[$value] ?? 'slate'">{{ $label }}</x-ui.badge>
