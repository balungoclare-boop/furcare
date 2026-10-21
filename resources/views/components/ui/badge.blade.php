@props(['tone' => 'neutral', 'icon' => null])

@php
    $tones = [
        'success' => 'bg-emerald-100 text-emerald-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'danger' => 'bg-rose-100 text-rose-700',
        'info' => 'bg-sky-100 text-sky-800',
        'neutral' => 'bg-slate-100 text-slate-700',
        'brand' => 'bg-indigo-100 text-indigo-700',
    ];
    $toneClass = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex min-h-6 items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {$toneClass}"]) }}>
    @if($icon)
        <x-ui.icon :name="$icon" class="h-3.5 w-3.5 shrink-0" />
    @endif
    {{ $slot }}
</span>
