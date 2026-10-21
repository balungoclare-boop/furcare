@props(['title' => 'No records found.', 'description' => 'There is currently no data to display.'])

<div class="rounded-2xl border border-dashed border-jade-200 bg-jade-50/70 p-8 text-center animate-rise">
    <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
    <p class="mt-2 text-sm text-slate-500">{{ $description }}</p>
    @if(trim($slot))
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
