@props(['lines' => 3])

<div {{ $attributes->merge(['class' => 'animate-pulse space-y-3']) }}>
    @for ($i = 0; $i < $lines; $i++)
        <div class="h-4 rounded bg-slate-200" style="width: {{ ['100%', '83%', '67%'][$i % 3] }}"></div>
    @endfor
</div>
