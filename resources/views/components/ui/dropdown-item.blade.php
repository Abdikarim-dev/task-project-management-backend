<a {{ $attributes->merge(['class' => 'flex items-center gap-2 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-slate-900', 'role' => 'menuitem']) }}>
    {{ $slot }}
</a>
