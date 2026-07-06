@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : $status;
    $label = $status instanceof \BackedEnum ? $status->label() : ucfirst(str_replace('_', ' ', $value));
    $colors = [
        'planning' => 'blue',
        'active' => 'green',
        'completed' => 'slate',
        'on_hold' => 'amber',
        'to_do' => 'slate',
        'in_progress' => 'brand',
    ];
@endphp

<x-ui.badge :color="$colors[$value] ?? 'slate'">{{ $label }}</x-ui.badge>
