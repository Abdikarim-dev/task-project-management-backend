@props(['title' => 'No results found', 'description' => 'Try adjusting your search or filters.'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50/50 px-6 py-16 text-center']) }}>
    <div class="mb-4 rounded-full bg-slate-100 p-4 text-slate-400">
        <x-ui.icon name="inbox" class="h-8 w-8" />
    </div>
    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $description }}</p>
    @isset($action)
        <div class="mt-6">{{ $action }}</div>
    @endisset
</div>
