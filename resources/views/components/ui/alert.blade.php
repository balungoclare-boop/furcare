@props(['type' => 'info'])

@php
    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-rose-200 bg-rose-50 text-rose-800',
        'warning' => 'border-jade-100 bg-jade-50 text-jade-700',
        'info' => 'border-slate-200 bg-slate-50 text-slate-700',
    ][$type] ?? 'border-slate-200 bg-slate-50 text-slate-800';
@endphp

<div {{ $attributes->merge(['class' => "rounded-xl border px-4 py-3 text-sm shadow-sm animate-rise {$styles}"]) }}>
    {{ $slot }}
</div>
