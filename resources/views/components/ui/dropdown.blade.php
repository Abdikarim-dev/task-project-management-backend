@props(['align' => 'right'])

<div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
    <button type="button" @click="open = !open" {{ $attributes->merge(['class' => 'inline-flex items-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500']) }} aria-haspopup="true" :aria-expanded="open">
        {{ $trigger ?? '' }}
        @unless(isset($trigger))
            <x-ui.icon name="dots" class="h-5 w-5" />
        @endunless
    </button>
    <div
        x-show="open"
        x-cloak
        x-transition
        @class([
            'absolute z-20 mt-2 w-48 origin-top-right rounded-lg border border-slate-200 bg-white py-1 shadow-lg focus:outline-none',
            'right-0' => $align === 'right',
            'left-0' => $align === 'left',
        ])
        role="menu"
    >
        {{ $slot }}
    </div>
</div>
