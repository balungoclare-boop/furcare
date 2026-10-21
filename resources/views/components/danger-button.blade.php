<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-jade-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-jade-600 active:bg-jade-700 focus:outline-none focus:ring-2 focus:ring-jade-400 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
