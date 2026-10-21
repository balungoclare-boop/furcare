@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-indigo-100 focus:border-indigo-500 focus:ring-indigo-300 rounded-lg shadow-sm']) }}>
