@props([
    'label',
    'value',
    'variant' => 'neutral',
    'icon' => null,
    'href' => null,
])

@php
    $isCritical = $variant === 'critical';
    $cardClasses = $isCritical
        ? 'border-rose-200 bg-rose-50/70'
        : 'border-slate-200 bg-white';
    $iconClasses = $isCritical
        ? 'bg-rose-100 text-rose-800'
        : 'bg-indigo-50 text-indigo-700';
@endphp

<article {{ $attributes->merge(['class' => "relative rounded-2xl border p-5 shadow-sm transition hover:shadow-md {$cardClasses}"]) }}>
    <div class="flex min-h-9 items-center justify-between gap-3">
        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-700">{{ $label }}</p>
        @if($icon)
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $iconClasses }}">
                <x-ui.icon :name="$icon" class="h-5 w-5" />
            </span>
        @endif
    </div>
    <p class="mt-3 text-3xl font-bold {{ $isCritical ? 'text-rose-900' : 'text-slate-900' }}">{{ $value }}</p>
    @if($href)
        <a
            href="{{ $href }}"
            class="absolute inset-0 rounded-2xl focus-visible:ring-2 focus-visible:ring-indigo-600 focus-visible:ring-offset-2"
            aria-label="{{ $label }}: {{ $value }}"
        ></a>
    @endif
</article>
