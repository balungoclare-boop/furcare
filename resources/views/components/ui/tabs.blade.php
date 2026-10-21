@props(['items', 'active'])

<nav {{ $attributes->merge(['class' => 'flex flex-wrap gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm']) }} aria-label="Page sections">
    @foreach($items as $item)
        @php($isActive = (string) $active === (string) $item['value'])
        <a
            href="{{ $item['href'] }}"
            @if($isActive) aria-current="page" @endif
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg px-3 text-xs font-semibold uppercase tracking-[0.1em] transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 sm:text-sm {{ $isActive ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-700' }}"
        >
            {{ $item['label'] }}
            @isset($item['count'])
                <span class="inline-flex min-w-6 items-center justify-center rounded-full px-1.5 py-0.5 text-xs font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                    {{ $item['count'] }}
                </span>
            @endisset
        </a>
    @endforeach
</nav>
