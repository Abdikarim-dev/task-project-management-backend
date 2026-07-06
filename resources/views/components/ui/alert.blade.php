@props(['type' => 'success', 'dismissible' => true])

@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
    $icons = [
        'success' => 'check-circle',
        'error' => 'exclamation',
        'warning' => 'exclamation',
        'info' => 'bell',
    ];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm '.($styles[$type] ?? $styles['info'])]) }}
    role="alert"
>
    <x-ui.icon :name="$icons[$type] ?? 'bell'" class="mt-0.5 h-5 w-5 shrink-0" />
    <div class="flex-1">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" @click="show = false" class="shrink-0 rounded p-1 hover:bg-black/5" aria-label="Dismiss">
            <x-ui.icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>
