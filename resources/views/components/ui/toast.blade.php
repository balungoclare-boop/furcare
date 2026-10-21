@props(['type' => 'success', 'message'])

@php
    $isError = $type === 'error';
    $style = $isError
        ? 'border-rose-200 bg-white text-rose-800'
        : 'border-emerald-300 bg-emerald-50 text-emerald-950';
    $iconName = $isError ? 'alert' : 'check';
    $iconStyle = $isError ? 'text-rose-700' : 'text-emerald-800';
    $dismissStyle = $isError
        ? 'text-rose-700 hover:bg-rose-100 focus-visible:outline-rose-700'
        : 'text-emerald-800 hover:bg-emerald-100 focus-visible:outline-emerald-700';
@endphp

<div
    x-data="{ visible: true }"
    x-show="visible"
    x-transition.opacity
    x-init="setTimeout(() => visible = false, 5000)"
    role="{{ $isError ? 'alert' : 'status' }}"
    aria-live="{{ $isError ? 'assertive' : 'polite' }}"
    class="fixed right-4 top-4 z-[90] flex max-w-sm items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg {{ $style }}"
>
    <x-ui.icon :name="$iconName" class="mt-0.5 h-5 w-5 shrink-0 {{ $iconStyle }}" />
    <p class="flex-1 font-medium">{{ $message }}</p>
    <button
        type="button"
        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 {{ $dismissStyle }}"
        aria-label="Dismiss notification"
        x-on:click="visible = false"
    >
        <span aria-hidden="true">&times;</span>
    </button>
</div>
