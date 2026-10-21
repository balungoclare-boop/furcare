<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex w-fit min-h-10 items-center justify-center gap-2 rounded-lg border border-jade-200 bg-jade-100 px-4 py-2 text-xs font-bold uppercase tracking-widest text-indigo-700 shadow-sm transition hover:bg-jade-200 focus:outline-none focus:ring-2 focus:ring-jade-300 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
