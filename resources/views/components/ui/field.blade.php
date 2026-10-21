@props([
    'label',
    'for',
    'hint' => null,
    'error' => null,
    'required' => false,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    <label for="{{ $for }}" class="block text-sm font-semibold text-slate-700">
        {{ $label }}
        @if($required)
            <span class="text-rose-700" aria-hidden="true">*</span>
            <span class="sr-only">Required</span>
        @endif
    </label>
    {{ $slot }}
    @if($hint)
        <p class="text-sm text-slate-600">{{ $hint }}</p>
    @endif
    @if($error)
        <p class="text-sm font-medium text-rose-700" role="alert">{{ $error }}</p>
    @endif
</div>
