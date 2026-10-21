@props(['href' => null, 'variant' => 'primary'])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white shadow-md shadow-indigo-200/60 hover:bg-indigo-700',
        'secondary' => 'border border-indigo-100 bg-white text-indigo-700 shadow-sm hover:border-indigo-200 hover:bg-indigo-50',
        'danger' => 'bg-rose-600 text-white shadow-md shadow-rose-200/60 hover:bg-rose-700',
    ];
    $classes = 'inline-flex w-fit min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-xs font-bold transition hover-lift focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-offset-2 sm:text-sm '.($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
