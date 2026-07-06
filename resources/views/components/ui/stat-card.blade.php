@props(['label', 'value', 'icon' => 'chart', 'color' => 'brand'])

@php
    $colors = [
        'brand' => 'bg-brand-50 text-brand-600',
        'green' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'red' => 'bg-red-50 text-red-600',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'card p-5 transition hover:shadow-md']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
            @isset($trend)
                <p class="mt-1 text-xs text-slate-500">{{ $trend }}</p>
            @endisset
        </div>
        <div class="rounded-lg p-2.5 {{ $colors[$color] ?? $colors['brand'] }}">
            <x-ui.icon :name="$icon" class="h-5 w-5" />
        </div>
    </div>
</div>
