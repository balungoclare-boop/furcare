<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex w-fit min-h-10 items-center justify-center gap-2 rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-xs font-bold uppercase tracking-widest text-white shadow-md shadow-indigo-200/60 transition hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
