@props([
    'route',
    'icon',
    'active' => null,
    'badge' => null,
])

@php
    $isActive = $active === null
        ? request()->routeIs($route)
        : request()->routeIs(...(array) $active);
@endphp

<a
    href="{{ route($route) }}"
    @if($isActive) aria-current="page" @endif
    {{ $attributes->merge([
        'class' => 'group flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 '.($isActive
            ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200'
            : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-700'),
    ]) }}
>
    <x-ui.icon
        :name="$icon"
        class="h-5 w-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 group-hover:text-indigo-600' }}"
    />
    <span class="min-w-0 flex-1">{{ $slot }}</span>
    @if($badge)
        <span
            class="inline-flex min-w-6 items-center justify-center rounded-full bg-rose-700 px-2 py-1 text-xs font-bold leading-none text-white"
            role="status"
            aria-label="{{ $badge }} pending appointments"
        >
            {{ $badge }}
        </span>
    @endif
</a>
