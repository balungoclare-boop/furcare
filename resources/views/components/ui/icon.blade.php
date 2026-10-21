@props(['name', 'class' => 'h-5 w-5'])

@php
    $paths = [
        'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-3H4v3Z" />',
        'pets' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm5-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm5 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8.1 14.8c1.9-2 5.9-2 7.8 0 2 2.1 1.8 5.4-.3 6.9-1.4.9-2.7.6-3.6.2-.9-.4-1.7-.4-2.6 0-.9.4-2.2.7-3.6-.2-2.1-1.5-2.3-4.8.3-6.9Z" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 3v4m8-4v4M4 10h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />',
        'syringe' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m14 5 5 5m-8-2 5 5m-9 7 9-9m-5-5 3-3 5 5-3 3M4 20l2-2" />',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l4 4v14H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6" />',
        'hospital' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 21h16M6 21V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v15M10 9h4m-2-2v4m-3 6h6" />',
        'medicine' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 19 9-9m-5-5 5-2 4 4-2 5m-9-7 7 7M3 21h6" />',
        'megaphone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 14v-4l13-5v14L4 14Zm0 0 2 6h3l-2-5m10-6a3 3 0 0 1 0 6" />',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 20v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-3a3 3 0 0 0-1 5.8M21 20v-2a4 4 0 0 0-3-3.87" />',
        'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3 4 6v5c0 5 3.4 8.5 8 10 4.6-1.5 8-5 8-10V6l-8-3Zm-3 9 2 2 4-4" />',
        'clock' => '<circle cx="12" cy="12" r="9" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 7v5l3 2" />',
        'alert' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 4.8 2.8 18a1.5 1.5 0 0 0 1.3 2.2h15.8a1.5 1.5 0 0 0 1.3-2.2L13.7 4.8a2 2 0 0 0-3.4 0Z" />',
        'bell' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Zm-8 12h4" />',
        'plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5v14m-7-7h14" />',
        'search' => '<circle cx="11" cy="11" r="7" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m20 20-4-4" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6" />',
        'chevron' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 18 6-6-6-6" />',
        'menu' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />',
    ];
@endphp

<svg xmlns="http://www.w3.org/2000/svg" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
    {!! $paths[$name] ?? $paths['document'] !!}
</svg>
