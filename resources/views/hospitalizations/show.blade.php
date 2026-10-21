<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $hospitalization->pet?->pet_name ?? 'Hospitalization Record' }}</h1>
            <p class="text-sm text-slate-500">
                Status:
                <span class="font-semibold {{ $hospitalization->status === \App\Models\Hospitalization::STATUS_ACTIVE ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ ucfirst($hospitalization->status) }}
                </span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('hospitalizations.edit', $hospitalization)" variant="secondary">Edit</x-ui.button>
            <x-ui.button :href="route('hospitalizations.index')" variant="secondary">Back</x-ui.button>
        </div>
    </div>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Admitted" :value="optional($hospitalization->admitted_date)->format('M d, Y') ?: 'N/A'" />
        <x-ui.stat-card label="Discharge Date" :value="optional($hospitalization->discharge_date)->format('M d, Y') ?: 'N/A'" />
        <x-ui.stat-card label="Progress Notes" :value="$hospitalization->progressNotes->count()" icon="document" />
    </section>

    <x-ui.card title="Hospitalization Details">
        <div class="space-y-3 text-sm text-slate-700">
            <p><span class="font-semibold text-slate-900">Owner:</span> {{ $hospitalization->pet?->owner_name ?? 'N/A' }}</p>
            <p><span class="font-semibold text-slate-900">Admission Notes:</span> {{ $hospitalization->notes ?: 'N/A' }}</p>
            <p><span class="font-semibold text-slate-900">Medication Schedule:</span> {{ $hospitalization->medication_schedule ?: 'N/A' }}</p>
            <p><span class="font-semibold text-slate-900">Discharge Summary:</span> {{ $hospitalization->discharge_summary ?: 'N/A' }}</p>
        </div>
    </x-ui.card>

    <x-ui.card title="Add Daily Progress Note">
        <form method="POST" action="{{ route('hospitalizations.progress-notes.store', $hospitalization) }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @csrf
            <div>
                <x-input-label for="note_date" :value="__('Note Date')" />
                <x-text-input id="note_date" name="note_date" type="date" class="mt-1 block w-full" :value="old('note_date', now()->toDateString())" required />
                <x-input-error :messages="$errors->get('note_date')" class="mt-2" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="notes" :value="__('Progress Notes')" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-slate-300" required>{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
            <div class="md:col-span-2">
                <x-ui.button type="submit">Save Progress Note</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card title="Progress Timeline">
        @if($hospitalization->progressNotes->isEmpty())
            <x-ui.empty-state
                title="No progress notes yet."
                description="Add daily notes to monitor confinement progress."
            />
        @else
            <div class="space-y-3">
                @foreach($hospitalization->progressNotes as $note)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-xs uppercase tracking-[0.1em] text-slate-500">{{ optional($note->note_date)->format('M d, Y') }}</p>
                            <p class="text-xs text-slate-500">{{ $note->user?->name ?? 'System' }}</p>
                        </div>
                        <p class="mt-2 text-sm text-slate-700">{{ $note->notes }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
