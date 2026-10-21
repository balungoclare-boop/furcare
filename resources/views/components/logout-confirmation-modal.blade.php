@props([
    'name' => 'confirm-logout',
])

<div
    x-data="{ show: false, trigger: null, close() { this.show = false; if (this.trigger) this.trigger.focus(); this.trigger = null } }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') { trigger = document.activeElement; show = true; $nextTick(() => $refs.cancel.focus()) }"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') close()"
    x-on:keydown.escape.window="if (show) close()"
    x-effect="document.body.classList.toggle('overflow-hidden', show)"
>
    <div
        x-cloak
        x-show="show"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4"
        style="display: none;"
        aria-labelledby="logout-modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
            x-on:click="close()"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <div
            class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
            x-on:keydown.tab.prevent="if ($event.shiftKey) { if (document.activeElement === $refs.cancel) $refs.confirm.focus(); else $refs.cancel.focus() } else { if (document.activeElement === $refs.confirm) $refs.cancel.focus(); else $refs.confirm.focus() }"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <h2 id="logout-modal-title" class="text-lg font-bold text-slate-900">Confirm Logout</h2>
            <p class="mt-2 text-sm text-slate-600">Are you sure you want to log out?</p>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    x-ref="cancel"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    x-on:click="close()"
                >
                    Cancel
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        x-ref="confirm"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        Confirm Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
