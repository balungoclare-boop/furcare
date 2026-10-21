@props(['title', 'description' => null])

<section {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <header class="border-b border-slate-200 pb-3">
        <h2 class="text-lg font-bold text-slate-900">{{ $title }}</h2>
        @if($description)
            <p class="mt-1 text-sm text-slate-600">{{ $description }}</p>
        @endif
    </header>
    {{ $slot }}
</section>
