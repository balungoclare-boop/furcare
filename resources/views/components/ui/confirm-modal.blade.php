<div
    x-data="{ open: false, message: '', pendingForm: null, trigger: null, close() { this.open = false; this.pendingForm = null; if (this.trigger) this.trigger.focus(); this.trigger = null } }"
    x-on:confirm-form.window="trigger = document.activeElement; pendingForm = $event.detail.form; message = $event.detail.message; open = true; $nextTick(() => $refs.cancel.focus())"
    x-on:keydown.escape.window="if (open) close()"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
>
    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[80] flex items-center justify-center p-4"
        role="presentation"
    >
        <div
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
            aria-hidden="true"
            x-on:click="close()"
        ></div>
        <section
            x-show="open"
            x-transition
            x-on:keydown.tab.prevent="if ($event.shiftKey) { if (document.activeElement === $refs.cancel) $refs.confirm.focus(); else $refs.cancel.focus() } else { if (document.activeElement === $refs.confirm) $refs.cancel.focus(); else $refs.confirm.focus() }"
            class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-modal-title"
        >
            <h2 id="confirm-modal-title" class="text-lg font-bold text-slate-900">Please confirm</h2>
            <p class="mt-2 text-sm text-slate-700" x-text="message"></p>
            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button
                    x-ref="cancel"
                    type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                    x-on:click="close()"
                >
                    Cancel
                </button>
                <button
                    x-ref="confirm"
                    type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-rose-700 px-4 text-sm font-semibold text-white hover:bg-rose-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700"
                    x-on:click="if (pendingForm) pendingForm.submit(); open = false; pendingForm = null"
                >
                    Confirm
                </button>
            </div>
        </section>
    </div>
</div>
