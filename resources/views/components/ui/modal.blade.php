@props(['name', 'title' => 'Confirm action'])

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
>
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition class="relative z-10 w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-xl">
            <h2 class="text-lg font-semibold text-slate-900">{{ $title }}</h2>
            <div class="mt-2 text-sm text-slate-600">{{ $slot }}</div>
            @isset($footer)
                <div class="mt-6 flex justify-end gap-3">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>
