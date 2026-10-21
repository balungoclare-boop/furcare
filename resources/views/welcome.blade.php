<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW CREATION Animal Clinic and Diagnostic Center</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/clinic-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/clinic-logo.jpg') }}">
</head>

<body class="h-screen overflow-hidden font-sans">
    @php($latestAnnouncements = $latestAnnouncements ?? collect())
    @php($allAnnouncements = $allAnnouncements ?? collect())

    <main class="relative h-screen overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-jade-50">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-24 -top-24 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
            <div class="absolute -bottom-28 -right-28 h-96 w-96 rounded-full bg-jade-200/45 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto flex h-full max-w-7xl flex-col px-4 py-4 sm:px-6 lg:px-10">
            <div class="flex w-full items-center justify-end">
                <span class="animate-fade-in rounded-full border border-indigo-200/70 bg-white/90 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-indigo-900 shadow-lg shadow-indigo-300/30 sm:text-xs">
                    Emergency Hotline 0956-348-1378 &bull; Location: Narvacan, Ilocos Sur
                </span>
            </div>

            <section class="grid flex-1 grid-cols-1 items-center gap-8 py-3 md:grid-cols-2 md:gap-10 lg:gap-12">
                <div class="order-1 animate-rise text-left">
                    <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500">
                        Veterinary Clinic Management System
                    </p>

                    <h1 class="mt-4 font-outfit text-5xl font-extrabold leading-tight tracking-tight text-slate-900 md:text-6xl">
                        NEW CREATION
                        <span class="block text-indigo-600">Animal Clinic and Diagnostic Center</span>
                    </h1>

                    <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600 md:text-lg">
                        Consultation &bull; Vaccination &bull; Hospitalization &bull; Laboratory &bull; Pharmacy &bull; Emergency Care &bull; Physiotherapy &bull; Pet Grooming
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold uppercase tracking-[0.1em] text-white shadow-lg shadow-indigo-900/25 transition duration-200 hover:scale-105 hover:bg-indigo-700"
                        >
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-jade-500 px-8 py-3 text-sm font-bold uppercase tracking-[0.1em] text-white shadow-lg shadow-jade-900/25 transition duration-200 hover:scale-105 hover:bg-jade-600"
                            >
                                Register
                            </a>
                        @endif
                    </div>

                    <div class="mt-4 flex justify-center md:justify-start">
                        <button
                            id="about-us-button"
                            type="button"
                            class="mt-1 inline-flex flex-wrap items-center gap-2 rounded-xl border border-indigo-200 bg-white/85 px-6 py-2 text-sm font-semibold uppercase tracking-[0.08em] text-indigo-700 shadow-lg shadow-indigo-100/60 transition hover:scale-105 hover:bg-indigo-50"
                        >
                            <svg class="h-4 w-4 text-jade-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10A8 8 0 114.9 3.8a1 1 0 11.7 1.9A6 6 0 1016 10a1 1 0 112 0zM9 7a1 1 0 012 0v3a1 1 0 11-2 0V7zm1 8a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 15z" clip-rule="evenodd" />
                            </svg>
                            About Us
                        </button>
                    </div>

                    <div class="mt-4 max-w-xl space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-indigo-700">Latest Announcements</p>
                            @if($allAnnouncements->isNotEmpty())
                                <span class="rounded-full border border-indigo-200 bg-white px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.08em] text-indigo-700">
                                    {{ $allAnnouncements->count() }} posted
                                </span>
                            @endif
                        </div>

                        <div class="max-h-72 space-y-3 overflow-y-auto pr-1">
                            @forelse($latestAnnouncements as $announcement)
                                <button
                                    type="button"
                                    data-announcement-open
                                    data-announcement-title="{{ $announcement->title }}"
                                    data-announcement-description="{{ $announcement->description }}"
                                    data-announcement-date="{{ optional($announcement->created_at)->format('F d, Y h:i A') }}"
                                    data-announcement-posted-by="{{ $announcement->creator?->name ?? 'Clinic Staff' }}"
                                    data-announcement-publish-date="{{ optional($announcement->publish_at)->format('F d, Y h:i A') ?? optional($announcement->created_at)->format('F d, Y h:i A') }}"
                                    data-announcement-expiry-date="{{ optional($announcement->expires_at)->format('F d, Y h:i A') }}"
                                    data-announcement-image="{{ $announcement->image_path ? asset('storage/'.$announcement->image_path) : '' }}"
                                    data-announcement-priority="{{ $announcement->priority }}"
                                    data-announcement-pinned="{{ $announcement->is_pinned ? '1' : '0' }}"
                                    class="group w-full rounded-xl border border-indigo-100 bg-white/80 p-3 text-left shadow-lg shadow-indigo-100/60 backdrop-blur transition hover:scale-[1.01] hover:border-indigo-200"
                                >
                                    <div class="flex items-start gap-3">
                                        @if($announcement->image_path)
                                            <img
                                                src="{{ asset('storage/'.$announcement->image_path) }}"
                                                alt="{{ $announcement->title }}"
                                                class="h-16 w-16 flex-shrink-0 rounded-lg border border-indigo-100 bg-white object-contain p-1 shadow"
                                            >
                                        @endif

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-1">
                                                @if($announcement->is_pinned)
                                                    <span class="rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">PINNED</span>
                                                @endif

                                                @if($announcement->priority === 'important')
                                                    <span class="rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">IMPORTANT</span>
                                                @else
                                                    <span class="rounded-full bg-jade-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-jade-600">ANNOUNCEMENT</span>
                                                @endif
                                            </div>
                                            <h3 class="mt-1 truncate text-sm font-bold text-slate-900">{{ $announcement->title }}</h3>
                                            <p class="mt-1 text-xs text-slate-600 [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] overflow-hidden">
                                                {{ $announcement->description }}
                                            </p>
                                            <div class="mt-2 flex items-center justify-between gap-2">
                                                <p class="text-[11px] text-slate-500">
                                                    Published {{ optional($announcement->publish_at)->format('M d, Y') ?? optional($announcement->created_at)->format('M d, Y') }}
                                                </p>
                                                <span class="text-[11px] font-semibold text-indigo-600 transition group-hover:text-indigo-700">
                                                    View Details
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            @empty
                                <article class="rounded-xl border border-indigo-100 bg-white/80 p-3 shadow-lg shadow-indigo-100/50 backdrop-blur">
                                    <p class="text-sm font-semibold text-slate-800">No announcements yet.</p>
                                    <p class="mt-1 text-xs text-slate-600">Clinic updates will appear here once posted by admin or staff.</p>
                                </article>
                            @endforelse
                        </div>

                        @if($allAnnouncements->isNotEmpty())
                            <div class="flex justify-center pt-1">
                                <button
                                    id="view-all-announcements-button"
                                    type="button"
                                    class="group inline-flex flex-wrap items-center gap-2 rounded-xl border border-indigo-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.08em] text-indigo-700 shadow-md shadow-indigo-100/50 transition duration-200 hover:scale-105 hover:border-indigo-300 hover:bg-indigo-50"
                                >
                                    View All Announcements
                                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h9.586L10.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L13.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="order-2 relative flex items-center justify-center md:justify-end">
                    <div class="pointer-events-none absolute inset-0">
                        <svg class="absolute left-6 top-10 h-10 w-10 text-indigo-300 opacity-30 animate-float" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.3 5.9c1.2-1.9 4.6-1 4.8 1.4.1 1.4-.9 2.6-2.3 2.8-1.7.2-3.3-1.5-2.5-3.2zM6.1 9.9C4 10.3 2.6 7.8 4.2 6.2c1-.9 2.5-.9 3.5 0 1.1 1 .8 2.9-.6 3.7zM17.5 10.5c-.9-1.8.7-4 2.7-3.7 1.3.2 2.3 1.3 2.3 2.7 0 1.5-1.2 2.7-2.7 2.7-.9 0-1.8-.6-2.3-1.7zM6.7 14.5c2.1-1.9 8.4-1.9 10.5 0 1.8 1.6 1.6 4.9-.3 6.2-1.3.9-2.5.7-3.3.3-.9-.4-1.8-.4-2.7 0-.8.4-2 .6-3.3-.3-1.9-1.3-2.1-4.6-.9-6.2z"/>
                        </svg>
                        <svg class="absolute right-8 top-20 h-8 w-8 text-indigo-300 opacity-25 animate-float" style="animation-delay: .6s;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l1.8 5.2L19 9l-5.2 1.8L12 16l-1.8-5.2L5 9l5.2-1.8L12 2z"/>
                        </svg>
                        <svg class="absolute bottom-8 left-14 h-8 w-8 text-jade-300 opacity-25 animate-float" style="animation-delay: 1.2s;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l1.8 5.2L19 9l-5.2 1.8L12 16l-1.8-5.2L5 9l5.2-1.8L12 2z"/>
                        </svg>
                    </div>

                    <img
                        src="{{ asset('images/cute.jpg.png') }}"
                        alt="Veterinary pets"
                        onerror="this.onerror=null;this.src='{{ asset('images/clinic-hero.jpg.png') }}';"
                        class="relative mx-auto h-auto max-h-[58vh] w-auto max-w-full object-contain drop-shadow-2xl transition duration-500 hover:scale-105 md:max-h-[62vh] lg:max-h-[66vh]"
                    >
                </div>
            </section>

            <footer class="w-full border-t border-slate-300/80 pt-3 text-slate-800">
                <p class="text-center text-xs text-slate-800">&copy; 2026 Veterinary Clinic Management System</p>
            </footer>
        </div>
    </main>

    <div id="about-us-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/55 p-4 opacity-0 transition-opacity duration-200">
        <div id="about-us-modal-panel" class="w-full max-w-2xl translate-y-3 scale-[0.98] rounded-2xl border border-indigo-100 bg-white/90 opacity-0 shadow-2xl shadow-indigo-900/20 backdrop-blur-lg transition-all duration-200">
            <div class="flex items-center justify-between border-b border-indigo-100 bg-gradient-to-r from-indigo-50/90 via-white/80 to-jade-50/90 px-5 py-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-jade-600">Clinic Profile</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">About Us</h2>
                </div>
                <button
                    type="button"
                    id="close-about-us-modal"
                    class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Close About Us modal"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="max-h-[70vh] space-y-4 overflow-y-auto px-5 py-5 text-sm leading-relaxed text-slate-700">
                <div class="rounded-xl border border-indigo-100 bg-white/80 p-4 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-indigo-700">Clinic Introduction</h3>
                    <p class="mt-1">
                        NEW CREATION Animal Clinic and Diagnostic Center provides compassionate and
                        professional veterinary care for pets. Our clinic offers consultation, vaccination, hospitalization,
                        laboratory diagnostics, pharmacy services, and emergency care.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 shadow-sm">
                        <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-indigo-700">Mission</h3>
                        <p class="mt-1 text-slate-700">
                            To provide high-quality veterinary care and promote responsible pet ownership while ensuring the
                            health and well-being of animals.
                        </p>
                    </div>

                    <div class="rounded-xl border border-jade-100 bg-jade-50/70 p-4 shadow-sm">
                        <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-jade-600">Vision</h3>
                        <p class="mt-1 text-slate-700">
                            To become a trusted veterinary clinic known for compassionate service and modern veterinary healthcare.
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-indigo-100 bg-white/80 p-4 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-indigo-700">Services</h3>
                    <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Consultation</span>
                        <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Vaccination</span>
                        <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Hospitalization</span>
                        <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Laboratory Diagnostics</span>
                        <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Pharmacy</span>
                        <span class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Emergency Pet Care</span>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3 text-xs text-slate-600">
                    Emergency Hotline: 0956-348-1378 &bull; Location: Narvacan, Ilocos Sur
                </div>
            </div>
        </div>
    </div>

    <div id="announcement-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/55 p-4 opacity-0 transition-opacity duration-200">
        <div id="announcement-modal-panel" class="w-full max-w-2xl translate-y-3 scale-[0.98] rounded-2xl border border-indigo-100 bg-white/90 opacity-0 shadow-2xl shadow-indigo-900/20 backdrop-blur-lg transition-all duration-200">
            <div class="flex items-center justify-between border-b border-indigo-100 bg-gradient-to-r from-indigo-50/90 via-white/80 to-jade-50/90 px-5 py-4">
                <div>
                    <div class="flex flex-wrap items-center gap-1">
                        <span id="announcement-modal-pinned-badge" class="hidden rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">PINNED</span>
                        <span id="announcement-modal-priority-badge" class="rounded-full bg-jade-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-jade-600">ANNOUNCEMENT</span>
                    </div>
                    <h2 id="announcement-modal-title" class="mt-1 text-lg font-bold text-slate-900">Announcement</h2>
                </div>
                <button
                    type="button"
                    id="close-announcement-modal"
                    class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Close announcement modal"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="max-h-[70vh] space-y-4 overflow-y-auto px-5 py-5 text-sm leading-relaxed text-slate-700">
                <img
                    id="announcement-modal-image"
                    src=""
                    alt="Announcement image"
                    class="hidden max-h-[65vh] w-full rounded-xl border border-indigo-100 bg-white object-contain p-1 shadow"
                >

                <p id="announcement-modal-date" class="text-xs font-semibold uppercase tracking-[0.1em] text-indigo-600"></p>

                <div class="rounded-xl border border-indigo-100 bg-white/80 p-4 shadow-sm">
                    <p id="announcement-modal-description" class="whitespace-pre-line text-slate-700"></p>
                </div>
            </div>
        </div>
    </div>

    <div id="all-announcements-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/55 p-4 opacity-0 transition-opacity duration-200">
        <div id="all-announcements-modal-panel" class="w-full max-w-4xl translate-y-3 scale-[0.98] rounded-2xl border border-indigo-100 bg-white/95 opacity-0 shadow-2xl shadow-indigo-900/20 backdrop-blur-lg transition-all duration-200">
            <div class="flex items-center justify-between border-b border-indigo-100 bg-gradient-to-r from-indigo-50/90 via-white/80 to-jade-50/90 px-5 py-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-700">Clinic Updates</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">All Announcements</h2>
                </div>
                <button
                    type="button"
                    id="close-all-announcements-modal"
                    class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Close all announcements modal"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="max-h-[72vh] space-y-4 overflow-y-auto px-5 py-5 text-sm leading-relaxed text-slate-700">
                @forelse($allAnnouncements as $announcement)
                    <article class="rounded-xl border border-indigo-100 bg-white/90 p-4 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row">
                            @if($announcement->image_path)
                                <img
                                    src="{{ asset('storage/'.$announcement->image_path) }}"
                                    alt="{{ $announcement->title }}"
                                    class="h-24 w-full rounded-lg border border-indigo-100 bg-white object-contain p-1 shadow sm:h-20 sm:w-20 sm:flex-shrink-0"
                                >
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1">
                                    @if($announcement->is_pinned)
                                        <span class="rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">PINNED</span>
                                    @endif
                                    @if($announcement->priority === 'important')
                                        <span class="rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">IMPORTANT</span>
                                    @else
                                        <span class="rounded-full bg-jade-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-jade-600">ANNOUNCEMENT</span>
                                    @endif
                                </div>

                                <h3 class="mt-2 text-base font-bold text-slate-900">{{ $announcement->title }}</h3>
                                <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $announcement->description }}</p>

                                <dl class="mt-3 grid grid-cols-1 gap-2 text-xs text-slate-600 sm:grid-cols-2">
                                    <div>
                                        <dt class="font-semibold uppercase tracking-[0.06em] text-slate-500">Posted By</dt>
                                        <dd class="mt-0.5 text-slate-700">{{ $announcement->creator?->name ?? 'Clinic Staff' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold uppercase tracking-[0.06em] text-slate-500">Posted Date</dt>
                                        <dd class="mt-0.5 text-slate-700">{{ optional($announcement->created_at)->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold uppercase tracking-[0.06em] text-slate-500">Published Date</dt>
                                        <dd class="mt-0.5 text-slate-700">{{ optional($announcement->publish_at)->format('M d, Y h:i A') ?? optional($announcement->created_at)->format('M d, Y h:i A') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold uppercase tracking-[0.06em] text-slate-500">Expiration Date</dt>
                                        <dd class="mt-0.5 text-slate-700">{{ optional($announcement->expires_at)->format('M d, Y h:i A') ?? 'No expiration date' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="rounded-xl border border-indigo-100 bg-white/80 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-slate-800">No announcements available.</p>
                        <p class="mt-1 text-xs text-slate-600">Clinic updates posted by staff and admin will appear here.</p>
                    </article>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        (() => {
            const showModal = (modal, panel) => {
                if (!modal || !panel) return;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                requestAnimationFrame(() => {
                    modal.classList.remove('opacity-0');
                    panel.classList.remove('translate-y-3', 'scale-[0.98]', 'opacity-0');
                });
            };

            const hideModal = (modal, panel) => {
                if (!modal || !panel) return;

                modal.classList.add('opacity-0');
                panel.classList.add('translate-y-3', 'scale-[0.98]', 'opacity-0');
                window.setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 200);
            };

            const aboutButton = document.getElementById('about-us-button');
            const aboutModal = document.getElementById('about-us-modal');
            const aboutPanel = document.getElementById('about-us-modal-panel');
            const aboutCloseButton = document.getElementById('close-about-us-modal');

            if (aboutButton && aboutModal && aboutPanel && aboutCloseButton) {
                aboutButton.addEventListener('click', () => showModal(aboutModal, aboutPanel));
                aboutCloseButton.addEventListener('click', () => hideModal(aboutModal, aboutPanel));
                aboutModal.addEventListener('click', (event) => {
                    if (event.target === aboutModal) {
                        hideModal(aboutModal, aboutPanel);
                    }
                });
            }

            const announcementTriggers = document.querySelectorAll('[data-announcement-open]');
            const announcementModal = document.getElementById('announcement-modal');
            const announcementPanel = document.getElementById('announcement-modal-panel');
            const announcementCloseButton = document.getElementById('close-announcement-modal');
            const announcementTitle = document.getElementById('announcement-modal-title');
            const announcementDate = document.getElementById('announcement-modal-date');
            const announcementDescription = document.getElementById('announcement-modal-description');
            const announcementImage = document.getElementById('announcement-modal-image');
            const announcementPriorityBadge = document.getElementById('announcement-modal-priority-badge');
            const announcementPinnedBadge = document.getElementById('announcement-modal-pinned-badge');
            const viewAllAnnouncementsButton = document.getElementById('view-all-announcements-button');
            const allAnnouncementsModal = document.getElementById('all-announcements-modal');
            const allAnnouncementsPanel = document.getElementById('all-announcements-modal-panel');
            const allAnnouncementsCloseButton = document.getElementById('close-all-announcements-modal');

            if (
                announcementTriggers.length > 0 &&
                announcementModal &&
                announcementPanel &&
                announcementCloseButton &&
                announcementTitle &&
                announcementDate &&
                announcementDescription &&
                announcementImage &&
                announcementPriorityBadge &&
                announcementPinnedBadge
            ) {
                announcementTriggers.forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        announcementTitle.textContent = trigger.dataset.announcementTitle || 'Announcement';
                        const postedBy = trigger.dataset.announcementPostedBy || 'Clinic Staff';
                        const postedDate = trigger.dataset.announcementDate || '';
                        const publishedDate = trigger.dataset.announcementPublishDate || '';
                        const expirationDate = trigger.dataset.announcementExpiryDate || '';

                        const metaParts = [`Posted by ${postedBy}`];
                        if (postedDate) {
                            metaParts.push(`Posted ${postedDate}`);
                        }
                        if (publishedDate) {
                            metaParts.push(`Published ${publishedDate}`);
                        }
                        metaParts.push(expirationDate ? `Expires ${expirationDate}` : 'No expiration date');

                        announcementDate.textContent = metaParts.join(' | ');
                        announcementDescription.textContent = trigger.dataset.announcementDescription || '';

                        const imageUrl = trigger.dataset.announcementImage || '';
                        if (imageUrl) {
                            announcementImage.src = imageUrl;
                            announcementImage.classList.remove('hidden');
                        } else {
                            announcementImage.src = '';
                            announcementImage.classList.add('hidden');
                        }

                        const isImportant = trigger.dataset.announcementPriority === 'important';
                        announcementPriorityBadge.textContent = isImportant ? 'IMPORTANT' : 'ANNOUNCEMENT';
                        announcementPriorityBadge.className = isImportant
                            ? 'rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white'
                            : 'rounded-full bg-jade-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-jade-600';

                        const isPinned = trigger.dataset.announcementPinned === '1';
                        announcementPinnedBadge.classList.toggle('hidden', !isPinned);

                        showModal(announcementModal, announcementPanel);
                    });
                });

                announcementCloseButton.addEventListener('click', () => hideModal(announcementModal, announcementPanel));
                announcementModal.addEventListener('click', (event) => {
                    if (event.target === announcementModal) {
                        hideModal(announcementModal, announcementPanel);
                    }
                });
            }

            if (
                viewAllAnnouncementsButton &&
                allAnnouncementsModal &&
                allAnnouncementsPanel &&
                allAnnouncementsCloseButton
            ) {
                viewAllAnnouncementsButton.addEventListener('click', () => showModal(allAnnouncementsModal, allAnnouncementsPanel));
                allAnnouncementsCloseButton.addEventListener('click', () => hideModal(allAnnouncementsModal, allAnnouncementsPanel));
                allAnnouncementsModal.addEventListener('click', (event) => {
                    if (event.target === allAnnouncementsModal) {
                        hideModal(allAnnouncementsModal, allAnnouncementsPanel);
                    }
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;

                if (aboutModal && aboutPanel && !aboutModal.classList.contains('hidden')) {
                    hideModal(aboutModal, aboutPanel);
                }

                if (announcementModal && announcementPanel && !announcementModal.classList.contains('hidden')) {
                    hideModal(announcementModal, announcementPanel);
                }

                if (allAnnouncementsModal && allAnnouncementsPanel && !allAnnouncementsModal.classList.contains('hidden')) {
                    hideModal(allAnnouncementsModal, allAnnouncementsPanel);
                }
            });
        })();
    </script>
</body>
</html>

