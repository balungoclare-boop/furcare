<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $pet->pet_name }}</h1>
            <p class="text-sm text-slate-500">
                {{ $pet->species }}{{ $pet->breed ? ' - '.$pet->breed : '' }} |
                Sex: {{ $pet->sex ? ucfirst($pet->sex) : 'N/A' }} |
                Owner: {{ $pet->owner_name }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('pets.edit', $pet)" variant="secondary">Edit</x-ui.button>
            <x-ui.button :href="route('pets.index')" variant="secondary">Back</x-ui.button>
        </div>
    </div>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Age" :value="$pet->display_age ?? 'N/A'" />
        <x-ui.stat-card label="Appointments" :value="$pet->appointments->count()" icon="calendar" />
        <x-ui.stat-card label="Vaccinations" :value="$pet->vaccinations->count()" icon="syringe" />
    </section>

    @if(auth()->user()?->isStaffOrAdmin())
        <x-ui.card title="Add Consultation Record" subtitle="Capture clinical findings for this pet.">
            <form method="POST" action="{{ route('medical-records.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf
                <input type="hidden" name="pet_id" value="{{ $pet->id }}">

                <div>
                    <x-input-label for="visit_date" :value="__('Visit Date')" />
                    <x-text-input id="visit_date" name="visit_date" type="date" class="mt-1 block w-full" :value="old('visit_date')" required />
                    <x-input-error :messages="$errors->get('visit_date')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="complaint" :value="__('Complaint')" />
                    <textarea id="complaint" name="complaint" rows="2" class="mt-1 block w-full rounded-md border-slate-300">{{ old('complaint') }}</textarea>
                    <x-input-error :messages="$errors->get('complaint')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="diagnosis" :value="__('Diagnosis')" />
                    <textarea id="diagnosis" name="diagnosis" rows="2" class="mt-1 block w-full rounded-md border-slate-300">{{ old('diagnosis') }}</textarea>
                    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="treatment" :value="__('Treatment')" />
                    <textarea id="treatment" name="treatment" rows="2" class="mt-1 block w-full rounded-md border-slate-300">{{ old('treatment') }}</textarea>
                    <x-input-error :messages="$errors->get('treatment')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-ui.button type="submit">Save Record</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    @endif

    <x-ui.card title="Consultation History">
        @if($pet->medicalRecords->isEmpty())
            <x-ui.empty-state
                title="No consultation records yet."
                description="Clinical records will appear here after each visit."
            />
        @else
            <div class="space-y-3">
                @foreach($pet->medicalRecords as $record)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500">
                            {{ optional($record->visit_date)->format('M d, Y') ?? 'Visit date not set' }}
                        </p>
                        <p class="mt-2 text-sm"><span class="font-semibold text-slate-700">Complaint:</span> {{ $record->complaint }}</p>
                        <p class="mt-1 text-sm"><span class="font-semibold text-slate-700">Diagnosis:</span> {{ $record->diagnosis ?: 'N/A' }}</p>
                        <p class="mt-1 text-sm"><span class="font-semibold text-slate-700">Treatment:</span> {{ $record->treatment ?: 'N/A' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.card>

    <x-ui.card title="Medical Timeline" subtitle="Latest pet health events arranged by date.">
        @if($timelineEntries->isEmpty())
            <x-ui.empty-state
                title="No timeline entries yet."
                description="Vaccinations, checkups, grooming, and related updates will appear here."
            />
        @else
            <div class="relative ml-3 border-l-2 border-indigo-100 pl-6">
                <div class="space-y-5">
                    @foreach($timelineEntries as $entry)
                        <article class="relative rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <span class="absolute -left-[1.9rem] top-5 h-3 w-3 rounded-full bg-indigo-500 ring-4 ring-white"></span>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $entry['type_badge_class'] }}">
                                    {{ $entry['type'] }}
                                </span>
                                <span class="text-xs uppercase tracking-[0.1em] text-slate-500">
                                    {{ optional($entry['date'])->format('M d, Y') ?? 'Date unavailable' }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm font-medium text-slate-800">{{ $entry['description'] }}</p>
                            <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $entry['status_badge_class'] }}">
                                {{ $entry['status'] }}
                            </span>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </x-ui.card>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-ui.card title="Appointment Timeline">
            @if($pet->appointments->isEmpty())
                <x-ui.empty-state
                    title="No appointments yet."
                    description="Appointment requests and outcomes will appear here."
                />
            @else
                <div class="space-y-2">
                    @foreach($pet->appointments as $appointment)
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $appointment->purpose }}</p>
                                <p class="text-xs text-slate-500">{{ optional($appointment->appointment_date)->format('M d, Y') }}</p>
                            </div>
                            <x-ui.appointment-status-badge :appointment="$appointment" />
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        <x-ui.card title="Vaccination History">
            @if($pet->vaccinations->isEmpty())
                <x-ui.empty-state
                    title="No vaccinations yet."
                    description="Vaccination records and due dates will appear here."
                />
            @else
                <div class="space-y-2">
                    @foreach($pet->vaccinations as $vaccination)
                        <div class="rounded-lg border border-slate-200 px-3 py-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $vaccination->vaccine_name }}</p>
                            <p class="text-xs text-slate-500">
                                Given: {{ optional($vaccination->date_given)->format('M d, Y') }} |
                                Next due: {{ optional($vaccination->next_due_date)->format('M d, Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </section>
</x-app-layout>
