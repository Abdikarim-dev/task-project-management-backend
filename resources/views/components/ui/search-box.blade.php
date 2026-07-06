@props(['name' => 'search', 'placeholder' => 'Search...', 'value' => null])

<form method="GET" {{ $attributes->merge(['class' => 'relative w-full max-w-md']) }}>
    <label for="{{ $name }}" class="sr-only">{{ $placeholder }}</label>
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <x-ui.icon name="search" class="h-4 w-4 text-slate-400" />
    </div>
    <input
        type="search"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value ?? request($name) }}"
        placeholder="{{ $placeholder }}"
        class="form-input pl-10"
    />
    {{ $slot }}
</form>
