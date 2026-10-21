@props(['title' => null, 'subtitle' => null, 'animated' => true])

@php
    $baseClasses = 'rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm';
    $motionClasses = $animated ? ' hover-lift animate-fade-in' : '';
@endphp

<section {{ $attributes->merge(['class' => $baseClasses.$motionClasses]) }}>
    @if($title || $subtitle)
        <header class="mb-4">
            @if($title)
                <h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
