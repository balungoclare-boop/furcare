<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') === 'Laravel' ? 'NEW CREATION Animal Clinic and Diagnostic Center' : config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="icon" type="image/jpeg" href="{{ asset('images/clinic-logo.jpg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/clinic-logo.jpg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        @php($isAuthPage = request()->routeIs('login') || request()->routeIs('register'))

        <main class="relative min-h-screen overflow-hidden">
            <img
                src="{{ asset('images/clinic-hero.jpg.png') }}"
                alt="Veterinary clinic background"
                class="absolute inset-0 h-full w-full object-cover"
            >

            @if($isAuthPage)
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-700/70 via-teal-500/60 to-jade-500/65 animate-gradient-slow"></div>
                <div class="absolute inset-0 bg-black/55"></div>

                <div class="pointer-events-none absolute inset-0">
                    <svg class="absolute left-[8%] top-[18%] h-10 w-10 text-white opacity-10 animate-float" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9a2 2 0 11-4 0 2 2 0 014 0zm5-2a2 2 0 100-4 2 2 0 000 4zm5 2a2 2 0 11-4 0 2 2 0 014 0zM8.1 14.8c1.9-2 5.9-2 7.8 0 2 2.1 1.8 5.4-.3 6.9-1.4.9-2.7.6-3.6.2-.9-.4-1.7-.4-2.6 0-.9.4-2.2.7-3.6-.2-2.1-1.5-2.3-4.8.3-6.9z" />
                    </svg>

                    <svg class="absolute right-[10%] top-[22%] h-12 w-12 text-white opacity-10 animate-float" style="animation-delay: 0.8s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14M9 21v-4h6v4M10 9h4m-2-2v4" />
                    </svg>

                    <svg class="absolute left-[14%] bottom-[20%] h-10 w-10 text-white opacity-10 animate-float" style="animation-delay: 1.4s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-8 8a6 6 0 00-6 6h20a6 6 0 00-6-6H8z" />
                    </svg>

                    <svg class="absolute right-[15%] bottom-[18%] h-11 w-11 text-white opacity-10 animate-float" style="animation-delay: 2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 3l13 13m-3 3l-6-6m-3 3l-4 4m0 0l-2-2m2 2l2 2m2-2l2-2" />
                    </svg>
                </div>
            @else
                <div class="absolute inset-0 bg-black/60"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/40 via-transparent to-emerald-700/35"></div>
            @endif

            <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-5 sm:px-6 sm:py-8">
                <div class="{{ $isAuthPage ? 'w-full max-w-full sm:max-w-6xl rounded-2xl border border-white/30 bg-white/20 p-5 shadow-2xl backdrop-blur-lg animate-fade-in sm:p-10' : 'w-full max-w-md rounded-2xl bg-white/85 p-5 shadow-2xl backdrop-blur-md animate-fade-in sm:p-10' }}">
                    <a href="{{ route('home') }}" class="group mb-6 block text-center">
                        <span class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-white shadow-xl sm:h-28 sm:w-28">
                            <img
                                src="{{ asset('images/clinic-logo.jpg') }}"
                                alt="NEW CREATION Animal Clinic and Diagnostic Center logo"
                                class="h-16 w-16 object-contain drop-shadow-md transition duration-300 group-hover:scale-105 sm:h-20 sm:w-20"
                            >
                        </span>
                        <p class="font-outfit text-2xl font-bold tracking-tight sm:text-3xl {{ $isAuthPage ? 'text-white' : 'text-indigo-800' }}">
                            NEW CREATION
                        </p>
                        <p class="mt-1 text-[11px] font-bold uppercase tracking-[0.14em] sm:text-xs sm:tracking-[0.16em] {{ $isAuthPage ? 'text-indigo-100' : 'text-indigo-700' }}">
                            Animal Clinic and Diagnostic Center
                        </p>
                        <p class="mt-2 text-[11px] tracking-[0.07em] sm:text-xs sm:tracking-[0.08em] {{ $isAuthPage ? 'text-slate-100/90' : 'text-slate-600' }}">
                            Compassionate Veterinary Care You Can Trust
                        </p>
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </main>

    </body>
</html>
