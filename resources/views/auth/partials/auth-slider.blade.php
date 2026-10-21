@php($startOnSignup = ($startOn ?? '') === 'register')

<style>
    :root {
        --auth-gradient: linear-gradient(135deg, #3B3281 0%, #02BF95 45%, #02BF95 100%);
    }

    .auth-shell {
        position: relative;
        width: 100%;
        max-width: 1100px;
        margin-inline: auto;
        padding: 0 1.25rem;
        box-sizing: border-box;
    }

    .auth-slider {
        position: relative;
        width: 100%;
        max-width: 100%;
        min-height: clamp(540px, 70vh, 640px);
        border-radius: 28px;
        overflow: hidden;
        background: #f8fafc;
        box-shadow: 0 30px 60px -25px rgba(15, 23, 42, 0.45);
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        isolation: isolate;
        margin: 0 auto;
    }

    .auth-panel {
        position: relative;
        padding: clamp(1.75rem, 3vw, 2.75rem);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.58s ease, opacity 0.58s ease;
        background: linear-gradient(145deg, #ffffff, #f4f7fb);
        z-index: 2;
        width: 100%;
        box-sizing: border-box;
    }

    .auth-panel .form-card {
        width: min(420px, 100%);
    }

    .auth-panel h2 {
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .auth-panel p.lead {
        color: #6b7280;
    }

    .auth-panel .secondary-link {
        color: #0f172a;
        font-weight: 600;
    }

    .sign-in-panel {
        transform: translateX(0);
        opacity: 1;
    }

    .sign-up-panel {
        transform: translateX(100%);
        opacity: 0;
    }

    .auth-slider.is-active .sign-in-panel {
        transform: translateX(-100%);
        opacity: 0;
    }

    .auth-slider.is-active .sign-up-panel {
        transform: translateX(0);
        opacity: 1;
    }

    .overlay-container {
        position: absolute;
        inset: 0;
        left: 50%;
        width: 50%;
        overflow: hidden;
        transition: transform 0.6s ease;
        z-index: 3;
        pointer-events: none;
    }

    .overlay {
        position: relative;
        width: 200%;
        height: 100%;
        left: -100%;
        background: var(--auth-gradient);
        background-size: 220% 220%;
        animation: gradientFlow 12s ease infinite;
        transform: translateX(0);
        transition: transform 0.6s ease;
    }

    .overlay::before {
        content: "";
        position: absolute;
        inset: -20% -8% 45% 32%;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.32), rgba(255, 255, 255, 0));
        transform: skewX(-14deg);
        mix-blend-mode: screen;
        opacity: 0.7;
    }

    .overlay::after {
        content: "";
        position: absolute;
        inset: 12% 8% -18% -6%;
        background: radial-gradient(ellipse at top left, rgba(255, 255, 255, 0.35), transparent 55%);
        transform: skewX(-10deg);
    }

    .auth-slider.is-active .overlay-container {
        transform: translateX(-100%);
    }

    .auth-slider.is-active .overlay {
        transform: translateX(50%);
    }

    .overlay-panel {
        position: absolute;
        top: 0;
        height: 100%;
        width: 50%;
        padding: clamp(1.75rem, 4vw, 3rem);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #e0f2fe;
        transition: transform 0.6s ease, opacity 0.6s ease;
        pointer-events: auto;
    }

    .overlay-left {
        transform: translateX(-18%);
        left: 0;
    }

    .overlay-right {
        right: 0;
        transform: translateX(0);
    }

    .auth-slider.is-active .overlay-left {
        transform: translateX(0);
    }

    .auth-slider.is-active .overlay-right {
        transform: translateX(18%);
    }

    .ghost-btn {
        margin-top: 1rem;
        border: 1.5px solid rgba(255, 255, 255, 0.8);
        border-radius: 9999px;
        padding: 0.8rem 1.8rem;
        background: transparent;
        color: #fff;
        letter-spacing: 0.06em;
        font-weight: 700;
        transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ghost-btn:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px -18px rgba(255, 255, 255, 0.6);
    }

    .ghost-btn:focus-visible {
        outline: 2px solid #fefefe;
        outline-offset: 2px;
    }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        color: #e0f2fe;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
    }

    .divider-dot {
        width: 36px;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.6), transparent);
        margin: 0.75rem auto 1rem;
    }

    .accent-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.9rem;
        border-radius: 999px;
        background: linear-gradient(120deg, rgba(59, 50, 129, 0.16), rgba(2, 191, 149, 0.18));
        color: #0f172a;
        font-size: 0.82rem;
    }

    @keyframes gradientFlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @media (max-width: 980px) {
        .auth-slider {
            grid-template-columns: 1fr;
            min-height: unset;
        }

        .overlay-container {
            display: none;
        }

        .auth-panel {
            transform: translateX(0) !important;
            opacity: 1 !important;
            width: 100%;
        }

        .sign-up-panel {
            display: none;
        }

        .auth-slider.is-active .sign-up-panel {
            display: flex;
        }

        .auth-slider.is-active .sign-in-panel {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .auth-shell {
            padding: 0.75rem;
        }

        .auth-panel {
            padding: 1.5rem;
        }

        .auth-panel .form-card {
            width: 100%;
        }

        .auth-panel form .flex {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    }

    @media (max-width: 640px) {
        .auth-shell {
            padding: 0.75rem;
        }

        .auth-slider {
            border-radius: 20px;
        }

        .auth-panel {
            padding: 1.5rem;
        }

        .auth-panel form {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .auth-shell {
            padding: 0.5rem;
        }

        .auth-panel .form-card {
            width: 100%;
        }

        .auth-panel form .w-full,
        .auth-panel form input,
        .auth-panel form button,
        .auth-panel form .block {
            width: 100%;
        }

        .auth-panel form .flex {
            flex-wrap: wrap;
        }

        .auth-panel h2 {
            font-size: 1.6rem;
        }

        .auth-panel .lead {
            font-size: 0.95rem;
        }
    }
</style>

<div class="auth-shell">
    <div id="authSlider" class="auth-slider {{ $startOnSignup ? 'is-active' : '' }}">
        <div class="auth-panel sign-in-panel">
            <div class="form-card">
                <p class="accent-chip mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Secure Staff Access
                </p>

                <h2 class="text-3xl text-slate-900">Welcome Back</h2>
                <p class="lead mt-2 text-sm">Sign in to continue managing patient records and appointments.</p>

                <!-- Session Status -->
                <x-auth-session-status class="mt-4 mb-2" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" class="text-slate-700" :value="__('Email')" />
                        <div class="mt-1 flex items-center rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 6 9-6m-18 10h18a2 2 0 002-2V9a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            <input
                                id="email"
                                class="w-full border-0 bg-transparent p-0 text-slate-800 outline-none ring-0 placeholder:text-slate-400 focus:ring-0"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="you@example.com"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="signin_password" class="text-slate-700" :value="__('Password')" />
                        <div class="mt-1 flex items-center rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            <input
                                id="signin_password"
                                class="min-w-0 flex-1 border-0 bg-transparent p-0 text-slate-800 outline-none ring-0 placeholder:text-slate-400 focus:ring-0"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                            >
                            <button
                                type="button"
                                class="ml-3 shrink-0 text-slate-500 transition hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                data-password-toggle
                                aria-controls="signin_password"
                                aria-label="{{ __('Show password') }}"
                                aria-pressed="false"
                            >
                                <svg data-password-icon="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                                </svg>
                                <svg data-password-icon="hide" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6.25 0 9.75 7 9.75 7a16.5 16.5 0 01-3.1 3.9M6.2 6.2C3.6 8 2.25 12 2.25 12s3.5 7 9.75 7c1.1 0 2.1-.2 3-.6" />
                                </svg>
                            </button>
                        </div>

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-slate-300 bg-white text-indigo-600 shadow-sm focus:ring-indigo-400" name="remember">
                            <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm">
                        <div class="flex items-center gap-2">
                            @if (Route::has('password.request'))
                                <a class="font-medium text-indigo-600 underline underline-offset-2 transition hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>
                        @if (Route::has('register'))
                            <button type="button" class="secondary-link transition hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" data-switch-to="signup">
                                {{ __('Create client account') }}
                            </button>
                        @endif
                    </div>

                    <div class="flex justify-start pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-10 py-3 text-sm font-semibold uppercase tracking-[0.1em] text-white shadow-lg shadow-indigo-900/30 transition duration-200 hover:scale-105 hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-900/45 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-offset-0"
                        >
                            {{ __('Log In') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="auth-panel sign-up-panel">
            <div class="form-card">
                <p class="accent-chip mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-jade-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-2.21 1.79-4 4-4s4 1.79 4 4-1.79 4-4 4-4-1.79-4-4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11V5a3 3 0 00-6 0v1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 8h2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 16h2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-2 2-2-2" />
                    </svg>
                    New Client Signup
                </p>

                <h2 class="text-3xl text-slate-900">Create Pet Owner Account</h2>
                <p class="lead mt-2 text-sm">Register to book appointments, view results, and manage your pets online.</p>

                <form method="POST" action="{{ route('register') }}" class="mt-5 space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="signup_password" :value="__('Password')" />

                        <div class="relative mt-1">
                            <x-text-input id="signup_password" class="block w-full pr-12"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password"
                                            aria-describedby="registration-password-guidance" />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-3 inline-flex items-center text-slate-500 transition hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                data-password-toggle
                                aria-controls="signup_password"
                                aria-label="{{ __('Show password') }}"
                                aria-pressed="false"
                            >
                                <svg data-password-icon="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                                </svg>
                                <svg data-password-icon="hide" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6.25 0 9.75 7 9.75 7a16.5 16.5 0 01-3.1 3.9M6.2 6.2C3.6 8 2.25 12 2.25 12s3.5 7 9.75 7c1.1 0 2.1-.2 3-.6" />
                                </svg>
                            </button>
                        </div>

                        <p id="registration-password-guidance" class="mt-1.5 text-sm text-slate-600">
                            Use at least 8 characters. For stronger security, choose a longer, unique password you do not use elsewhere.
                        </p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                        <div class="relative mt-1">
                            <x-text-input id="password_confirmation" class="block w-full pr-12"
                                            type="password"
                                            name="password_confirmation" required autocomplete="new-password" />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-3 inline-flex items-center text-slate-500 transition hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                data-password-toggle
                                aria-controls="password_confirmation"
                                aria-label="{{ __('Show password') }}"
                                aria-pressed="false"
                            >
                                <svg data-password-icon="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                                </svg>
                                <svg data-password-icon="hide" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6.25 0 9.75 7 9.75 7s-1 2-3.1 3.9M6.2 6.2C3.6 8 2.25 12 2.25 12s3.5 7 9.75 7c1.1 0 2.1-.2 3-.6" />
                                </svg>
                            </button>
                        </div>

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1 text-sm">
                        <button type="button" class="secondary-link transition hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" data-switch-to="login">
                            {{ __('Back to login') }}
                        </button>
                        <x-primary-button class="ms-4">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overlay-container" aria-hidden="true">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <div class="badge-pill mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                        </svg>
                        Fast check-ins
                    </div>
                    <h3 class="text-2xl font-semibold">Already with us?</h3>
                    <p class="mt-2 max-w-xs text-sm text-slate-100/90">Access appointments, billing, and lab results in one secure place.</p>
                    <div class="divider-dot"></div>
                    <button class="ghost-btn" data-switch-to="login">Login</button>
                </div>

                <div class="overlay-panel overlay-right">
                    <div class="badge-pill mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                        </svg>
                        New to the clinic
                    </div>
                    <h3 class="text-2xl font-semibold">Join our care circle</h3>
                    <p class="mt-2 max-w-xs text-sm text-slate-100/90">Create your account to book visits and track your pet’s wellness journey.</p>
                    <div class="divider-dot"></div>
                    <button class="ghost-btn" data-switch-to="signup">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.getAttribute('aria-controls'));
                if (!(input instanceof HTMLInputElement)) return;

                const isVisible = input.type === 'password';
                input.type = isVisible ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(isVisible));
                button.setAttribute('aria-label', isVisible ? @json(__('Hide password')) : @json(__('Show password')));
                button.querySelector('[data-password-icon="show"]').classList.toggle('hidden', isVisible);
                button.querySelector('[data-password-icon="hide"]').classList.toggle('hidden', !isVisible);
            });
        });

        const slider = document.getElementById('authSlider');
        if (!slider) return;

        const activate = (mode) => {
            if (mode === 'signup') {
                slider.classList.add('is-active');
            } else {
                slider.classList.remove('is-active');
            }
        };

        document.querySelectorAll('[data-switch-to="signup"]').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                activate('signup');
            });
        });

        document.querySelectorAll('[data-switch-to="login"]').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                activate('login');
            });
        });
    });
</script>
