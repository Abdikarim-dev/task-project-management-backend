@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1 text-sm text-slate-500">
        @foreach ($items as $index => $item)
            <li class="flex items-center gap-1">
                @if ($index > 0)
                    <x-ui.icon name="chevron-right" class="h-4 w-4 text-slate-400" />
                @endif
                @if (! empty($item['url']) && $index < count($items) - 1)
                    <a href="{{ $item['url'] }}" class="transition hover:text-brand-600">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-slate-900" @if($index === count($items) - 1) aria-current="page" @endif>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
