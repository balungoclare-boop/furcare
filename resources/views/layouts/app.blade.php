<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $clinicName = config('app.name') === 'Laravel'
            ? 'NEW CREATION Animal Clinic and Diagnostic Center'
            : config('app.name');
        $routeName = request()->route()?->getName() ?? '';
        $exactPageTitles = [
            'admin.dashboard' => 'Admin Dashboard',
            'staff.dashboard' => 'Clinic Dashboard',
            'client.dashboard' => 'My Dashboard',
            'appointments.accepted' => 'Accepted Appointments',
            'appointments.completed' => 'Completed Appointments',
            'appointments.grooming.create' => 'Book Grooming',
            'admin.audit-logs.index' => 'Audit Trail',
        ];
        $routeParts = array_values(array_filter(
            explode('.', $routeName),
            fn ($part) => ! in_array($part, ['admin', 'staff', 'client'], true)
        ));
        $routeAction = end($routeParts) ?: '';
        $routeResource = count($routeParts) > 1 ? $routeParts[count($routeParts) - 2] : $routeAction;
        $pageTitle = $exactPageTitles[$routeName] ?? match ($routeAction) {
            'index' => \Illuminate\Support\Str::headline($routeResource),
            'create' => 'Create '.\Illuminate\Support\Str::headline($routeResource),
            'edit' => 'Edit '.\Illuminate\Support\Str::headline($routeResource),
            'show' => \Illuminate\Support\Str::headline($routeResource).' Details',
            default => \Illuminate\Support\Str::headline(implode(' ', $routeParts)),
        };
        $dashboardRoute = auth()->user()->isAdmin()
            ? 'admin.dashboard'
            : (auth()->user()->isVeterinaryStaff() ? 'staff.dashboard' : 'client.dashboard');
        $isAdmin = auth()->user()->isAdmin();
        $isStaff = auth()->user()->isVeterinaryStaff();
        $clientNavGroups = [
            'Clinic' => [
                ['label' => 'Dashboard', 'route' => $dashboardRoute, 'icon' => 'dashboard', 'active' => ['admin.dashboard', 'staff.dashboard', 'client.dashboard']],
                ['label' => 'Pets', 'route' => 'pets.index', 'icon' => 'pets', 'active' => 'pets.*'],
                ['label' => 'Appointments', 'route' => 'appointments.index', 'icon' => 'calendar', 'active' => 'appointments.*', 'badge' => $clientPendingAppointments ?? 0],
                ['label' => 'Vaccinations', 'route' => 'vaccinations.index', 'icon' => 'syringe', 'active' => 'vaccinations.*'],
            ],
        ];
        $staffNavGroups = [
            'Clinic' => [
                ['label' => 'Dashboard', 'route' => $dashboardRoute, 'icon' => 'dashboard', 'active' => ['admin.dashboard', 'staff.dashboard', 'client.dashboard']],
                ['label' => 'Pets', 'route' => 'pets.index', 'icon' => 'pets', 'active' => 'pets.*'],
                ['label' => 'Appointments', 'route' => 'appointments.index', 'icon' => 'calendar', 'active' => 'appointments.*', 'badge' => $pendingAppointments ?? 0],
                ['label' => 'Vaccinations', 'route' => 'vaccinations.index', 'icon' => 'syringe', 'active' => 'vaccinations.*'],
                ['label' => 'Hospitalizations', 'route' => 'hospitalizations.index', 'icon' => 'hospital', 'active' => 'hospitalizations.*'],
            ],
            'Inventory' => [
                ['label' => 'Medicines', 'route' => 'medicines.index', 'icon' => 'medicine', 'active' => 'medicines.*'],
            ],
            'Communication' => [
                ['label' => 'Announcements', 'route' => 'announcements.index', 'icon' => 'megaphone', 'active' => 'announcements.*'],
            ],
            'Admin' => [
                ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'document', 'active' => 'reports.*'],
            ],
        ];
        if ($isAdmin) {
            $staffNavGroups['Admin'][] = ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'users', 'active' => 'admin.users.*'];
            $staffNavGroups['Admin'][] = ['label' => 'Audit Trail', 'route' => 'admin.audit-logs.index', 'icon' => 'shield', 'active' => 'admin.audit-logs.*'];
        }
        $navGroups = $isAdmin || $isStaff ? $staffNavGroups : $clientNavGroups;
    @endphp
    <title>@yield('title', $pageTitle) | {{ $clinicName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/clinic-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/clinic-logo.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft-pattern antialiased">
@auth
    <div
        x-data="{
            sidebarOpen: false,
            isMobile: window.matchMedia('(max-width: 767px)').matches,
            init() {
                window.addEventListener('resize', () => {
                    this.isMobile = window.matchMedia('(max-width: 767px)').matches;
                    if (!this.isMobile) this.sidebarOpen = false;
                });
            },
            openSidebar() { this.sidebarOpen = true; this.$nextTick(() => this.$refs.sidebar.querySelector('button, a[href]')?.focus()) },
            closeSidebar(restoreFocus = true) {
                this.sidebarOpen = false;
                if (restoreFocus && this.isMobile) this.$nextTick(() => this.$refs.menuButton.focus());
            },
            trapSidebarFocus(event) {
                if (!this.isMobile || !this.sidebarOpen || event.key !== 'Tab') return;
                const elements = [...this.$refs.sidebar.querySelectorAll('a[href], button:not([disabled])')];
                const first = elements[0];
                const last = elements[elements.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        }"
        x-effect="document.body.classList.toggle('overflow-hidden', isMobile && sidebarOpen)"
        class="relative min-h-screen"
        @keydown="trapSidebarFocus($event)"
        @keydown.escape.window="if (sidebarOpen) closeSidebar()"
    >
        <div
            class="fixed inset-0 z-40 bg-slate-900/55 backdrop-blur-sm md:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="closeSidebar()"
        ></div>

        <aside
            id="app-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-72 overflow-y-auto border-r border-indigo-100 bg-white/95 px-5 py-6 shadow-xl transition-transform duration-300 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            :inert="isMobile && !sidebarOpen"
            :aria-hidden="isMobile && !sidebarOpen"
            x-ref="sidebar"
        >
            <div class="flex items-center gap-3 border-b border-slate-100 pb-6">
                <img src="{{ asset('images/clinic-logo.jpg') }}" alt="Clinic Logo" class="h-11 w-11 rounded-xl object-cover shadow-md ring-4 ring-indigo-50">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-500">Care portal</p>
                    <p class="text-base font-extrabold text-slate-900">Vet Clinic</p>
                </div>
                <button
                    type="button"
                    class="ml-auto inline-flex h-11 w-11 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 md:hidden"
                    aria-label="Close navigation menu"
                    @click="closeSidebar()"
                >
                    <span aria-hidden="true" class="text-2xl leading-none">&times;</span>
                </button>
            </div>

            <nav class="mt-7 space-y-6" aria-label="Primary navigation" @click="if (isMobile) closeSidebar(false)">
                @foreach($navGroups as $group => $items)
                    <section class="space-y-1.5">
                        @if($group)
                            <h2 class="px-3 text-xs font-bold uppercase tracking-[0.16em] text-slate-500">{{ $group }}</h2>
                        @endif
                        @foreach($items as $item)
                            <x-ui.nav-item
                                :route="$item['route']"
                                :icon="$item['icon']"
                                :active="$item['active']"
                                :badge="$item['badge'] ?? null"
                            >
                                {{ $item['label'] }}
                            </x-ui.nav-item>
                        @endforeach
                    </section>
                @endforeach
            </nav>
        </aside>

        <div class="md:pl-72">
            <header class="sticky top-0 z-30 border-b border-indigo-100 bg-white/85 backdrop-blur">
                <div class="flex min-h-16 flex-wrap items-center justify-between gap-2 px-4 py-2 md:h-16 md:flex-nowrap md:py-0 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        x-ref="menuButton"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg border border-indigo-100 bg-white px-3 py-2 text-sm font-semibold text-indigo-700 shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 md:hidden"
                        aria-label="Open navigation menu"
                        :aria-expanded="sidebarOpen.toString()"
                        aria-controls="app-sidebar"
                        @click="openSidebar()"
                    >
                        <x-ui.icon name="menu" class="h-5 w-5" />
                    </button>

                    <div class="min-w-0 flex-1 md:flex-none">
                        <nav aria-label="Breadcrumb" class="hidden text-xs text-slate-500 md:block">
                            <a href="{{ route($dashboardRoute) }}" class="hover:text-indigo-700">Dashboard</a>
                            <span aria-hidden="true" class="px-1.5">/</span>
                            <span aria-current="page">{{ $pageTitle }}</span>
                        </nav>
                        <p class="truncate text-sm font-bold text-slate-900 md:mt-0.5 md:text-base">{{ $pageTitle }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <x-dropdown align="right" width="w-80 max-w-[calc(100vw-2rem)]" contentClasses="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                            <x-slot:trigger>
                                <button type="button" class="inline-flex min-h-11 items-center gap-2 rounded-xl px-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" aria-haspopup="true" :aria-expanded="open.toString()">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-800" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                    <span class="hidden max-w-32 truncate sm:inline">{{ auth()->user()->name }}</span>
                                    <x-ui.icon name="chevron" class="h-4 w-4 rotate-90 text-slate-500" />
                                    <span class="sr-only">Open account menu</span>
                                </button>
                            </x-slot:trigger>
                            <x-slot:content>
                                <div class="border-b border-slate-100 px-4 py-3">
                                    <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                                    <p class="mt-0.5 text-xs capitalize text-slate-600">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center px-4 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
                                    Profile
                                </a>
                                <button type="button" class="flex min-h-11 w-full items-center px-4 text-left text-sm font-medium text-rose-700 hover:bg-rose-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-rose-700" x-on:click="$dispatch('open-modal', 'confirm-logout')">
                                    Logout
                                </button>
                            </x-slot:content>
                        </x-dropdown>

                        <x-dropdown align="right" width="w-80 max-w-[calc(100vw-2rem)]" contentClasses="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                            <x-slot:trigger>
                                <button type="button" class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" aria-haspopup="true" :aria-expanded="open.toString()" aria-label="Notifications{{ $unreadNotificationsCount ? ', '.$unreadNotificationsCount.' unread' : '' }}">
                                    <x-ui.icon name="bell" class="h-5 w-5" />
                                    @if($unreadNotificationsCount)
                                        <span class="absolute right-0.5 top-0.5 inline-flex min-w-5 items-center justify-center rounded-full bg-rose-700 px-1 text-[10px] font-bold leading-5 text-white" aria-hidden="true">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                                    @endif
                                </button>
                            </x-slot:trigger>
                            <x-slot:content>
                                <div class="border-b border-slate-100 px-4 py-3">
                                    <p class="text-sm font-bold text-slate-900">Notifications</p>
                                    <p class="text-xs text-slate-600">{{ $unreadNotificationsCount }} unread</p>
                                </div>
                                <div class="max-h-96 overflow-y-auto p-2">
                                    @forelse($recentNotifications as $notification)
                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                            @csrf
                                            <button type="submit" class="flex min-h-14 w-full items-start gap-2 rounded-lg px-3 py-2 text-left hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600 {{ $notification->read_at ? '' : 'bg-indigo-50/60' }}">
                                                @if(!$notification->read_at)
                                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-indigo-700" aria-label="Unread"></span>
                                                @endif
                                                <span class="min-w-0 flex-1">
                                                    <span class="block text-sm font-medium text-slate-800">{{ $notification->data['message'] ?? 'You have a clinic update.' }}</span>
                                                    <span class="mt-1 block text-xs text-slate-600">{{ $notification->created_at->diffForHumans() }}</span>
                                                </span>
                                            </button>
                                        </form>
                                    @empty
                                        <p class="px-3 py-5 text-center text-sm text-slate-600">You’re all caught up.</p>
                                    @endforelse
                                </div>
                            </x-slot:content>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl space-y-4 animate-fade-in">
                    @isset($header)
                        <div class="rounded-2xl border border-indigo-100 bg-white/90 px-5 py-4 shadow-sm animate-rise">
                            {{ $header }}
                        </div>
                    @endisset

                    @if ($errors->any())
                        <x-ui.alert type="error">
                            Please review the highlighted form errors and try again.
                        </x-ui.alert>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-logout-confirmation-modal />
        @if(session('success'))
            <x-ui.toast type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-ui.toast type="error" :message="session('error')" />
        @endif
        <x-ui.confirm-modal />
    </div>
@else
    <main>
        {{ $slot }}
    </main>
@endauth

</body>
</html>
