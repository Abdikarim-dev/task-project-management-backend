@props(['name', 'size' => 'md'])

@php
    $sizes = ['sm' => 'h-8 w-8 text-xs', 'md' => 'h-10 w-10 text-sm', 'lg' => 'h-12 w-12 text-base'];
    $initials = strtoupper(collect(explode(' ', (string) $slot))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join(''));
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-700 '.($sizes[$size] ?? $sizes['md'])]) }} aria-hidden="true">
    {{ $initials }}
</div>
