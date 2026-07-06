@props(['label' => null, 'name', 'required' => false, 'rows' => 4])

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="text-red-500" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->except('class')->merge(['class' => 'form-input resize-y'.($errors->has($name) ? ' border-red-500 focus:border-red-500 focus:ring-red-500/20' : '')]) }}
    >{{ old($name, $slot) }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
    @enderror
</div>
