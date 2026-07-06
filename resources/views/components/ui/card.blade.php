@props(['title' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || isset($header))
        <div class="flex items-center justify-between border-b border-slate-200/80 px-6 py-4">
            @if (isset($header))
                {{ $header }}
            @else
                <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
            @endif
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    <div @class([$padding ? 'p-6' : ''])>
        {{ $slot }}
    </div>
</div>
