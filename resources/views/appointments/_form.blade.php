@props([
    'appointment' => null,
    'pets' => collect(),
    'appointmentType' => null,
    'allowTypeSelection' => false,
    'action',
    'method' => 'POST',
])

@php
    $user = auth()->user();
    $isClient = $user?->isClient();
    $type = old('type', $appointment?->type ?? $appointmentType ?? \App\Models\Appointment::TYPE_VACCINATION);

    $showTypeSelection = $allowTypeSelection && ! $appointment;
    $typeOptions = [
        \App\Models\Appointment::TYPE_VACCINATION => 'Vaccination Appointment',
        \App\Models\Appointment::TYPE_CHECKUP => 'Checkup Appointment',
    ];

    $isVaccination = $type === \App\Models\Appointment::TYPE_VACCINATION;
    $isCheckup = $type === \App\Models\Appointment::TYPE_CHECKUP;
    $isGrooming = $type === \App\Models\Appointment::TYPE_GROOMING;
    $groomingServices = \App\Models\Appointment::GROOMING_SERVICE_LABELS;
    $vaccineOptions = \App\Models\Appointment::VACCINE_PURPOSE_OPTIONS;

    $appointmentPurpose = old('purpose', $appointment?->purpose);
    $vaccinationPurpose = old('vaccination_purpose');
    if ($vaccinationPurpose === null) {
        if ($isVaccination && filled($appointmentPurpose)) {
            $vaccinationPurpose = in_array($appointmentPurpose, $vaccineOptions, true) ? $appointmentPurpose : 'Others';
        } else {
            $vaccinationPurpose = '';
        }
    }
    $otherVaccine = old('other_vaccine');
    if ($otherVaccine === null && $isVaccination && filled($appointmentPurpose) && ! in_array($appointmentPurpose, $vaccineOptions, true)) {
        $otherVaccine = $appointmentPurpose;
    }
    $checkupPurpose = old('checkup_purpose', $isCheckup ? $appointmentPurpose : null);

    $currentStatus = old('status', $appointment?->status ?? \App\Models\Appointment::STATUS_PENDING);
    if ($currentStatus === 'confirmed') {
        $currentStatus = \App\Models\Appointment::STATUS_APPROVED;
    }
    $isEmergency = (bool) old('is_emergency', (bool) ($appointment?->is_emergency ?? false));
    $preferredTime = old('preferred_time', $appointment?->preferred_time ? substr((string) $appointment->preferred_time, 0, 5) : null);
    $appointmentDate = old('appointment_date', optional($appointment?->appointment_date)->toDateString());
    $isStaffWalkInMode = ! $isClient && ! $appointment && ! request()->filled('pet_id');
@endphp

<form id="appointment-form" method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @if($showTypeSelection)
            <div class="md:col-span-2">
                <x-input-label for="appointment_type" :value="__('Appointment Category')" />
                <select id="appointment_type" name="type" class="mt-1 block w-full rounded-md border-slate-300" required>
                    @foreach($typeOptions as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}" @selected($type === $typeKey)>{{ $typeLabel }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>
        @else
            <input type="hidden" name="type" value="{{ $type }}">
        @endif

        @if($isStaffWalkInMode)
            <div>
                <x-input-label for="pet_name" :value="__('Pet Name')" />
                <x-text-input
                    id="pet_name"
                    name="pet_name"
                    class="mt-1 block w-full"
                    :value="old('pet_name')"
                    placeholder="Enter pet name (walk-in patient)"
                    required
                />
                <x-input-error :messages="$errors->get('pet_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="owner_name" :value="__('Owner Name')" />
                <x-text-input
                    id="owner_name"
                    name="owner_name"
                    class="mt-1 block w-full"
                    :value="old('owner_name')"
                    placeholder="Pet owner name"
                    required
                />
                <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_number" :value="__('Contact Number')" />
                <x-text-input
                    id="contact_number"
                    name="contact_number"
                    class="mt-1 block w-full"
                    :value="old('contact_number')"
                    placeholder="Owner contact number"
                    required
                />
                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="pet_id" :value="__('Pet')" />
                <select id="pet_id" name="pet_id" class="mt-1 block w-full rounded-md border-slate-300">
                    <option value="">Select Pet</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" @selected((string) old('pet_id', $appointment?->pet_id ?? request('pet_id')) === (string) $pet->id)>
                            {{ $pet->pet_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('pet_id')" class="mt-2" />
            </div>
        @endif

        @if($isClient)
            <div class="md:col-span-2">
                <p id="appointment_date_label" class="text-sm font-medium text-slate-700">{{ $isGrooming ? __('Preferred Date') : __('Appointment Date') }}</p>
                <div
                    id="appointment-calendar"
                    class="mt-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"
                    data-availability-url="{{ route('appointments.availability') }}"
                    data-selected-date="{{ $appointmentDate }}"
                    data-appointment-id="{{ $appointment?->id }}"
                    data-limit="{{ \App\Services\AppointmentAvailabilityService::DAILY_LIMIT }}"
                >
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <h3 id="appointment-calendar-month" class="text-lg font-bold text-slate-800"></h3>
                            <p class="text-xs text-slate-500">Select a date. Up to {{ \App\Services\AppointmentAvailabilityService::DAILY_LIMIT }} appointments are available each day.</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1.5">
                            <button type="button" data-calendar-today class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Today</button>
                            <button type="button" data-calendar-previous aria-label="Previous month" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">&lsaquo;</button>
                            <button type="button" data-calendar-next aria-label="Next month" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">&rsaquo;</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <div class="min-w-[35rem]">
                            <div class="grid grid-cols-7 border-l border-t border-slate-200 bg-slate-50 text-center text-xs font-semibold text-slate-500">
                                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                                    <div class="border-b border-r border-slate-200 py-2">{{ $weekday }}</div>
                                @endforeach
                            </div>
                            <div id="appointment-calendar-days" class="grid grid-cols-7 border-l border-slate-200" aria-label="Appointment availability calendar"></div>
                        </div>
                    </div>

                    <p id="appointment-calendar-selection" class="mt-3 min-h-5 text-sm font-semibold text-indigo-700" aria-live="polite"></p>
                    <p id="appointment-calendar-status" class="mt-1 min-h-5 text-sm text-rose-600" role="status" aria-live="polite"></p>
                </div>
                <input id="appointment_date" name="appointment_date" type="hidden" value="{{ $appointmentDate }}" required>
                <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label id="appointment_date_label" for="appointment_date" :value="$isGrooming ? __('Preferred Date') : __('Appointment Date')" />
                <x-text-input id="appointment_date" name="appointment_date" type="date" class="mt-1 block w-full" :value="$appointmentDate" required />
                <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
            </div>
        @endif

        <div id="vaccination-fields" class="md:col-span-2 {{ $isVaccination ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="vaccination_purpose" :value="__('Vaccine Purpose')" />
                    <select id="vaccination_purpose" name="vaccination_purpose" class="mt-1 block w-full rounded-md border-slate-300">
                        <option value="">Select Vaccine</option>
                        @foreach($vaccineOptions as $vaccine)
                            <option value="{{ $vaccine }}" @selected($vaccinationPurpose === $vaccine)>{{ $vaccine }}</option>
                        @endforeach
                        <option value="Others" @selected($vaccinationPurpose === 'Others')>Others</option>
                    </select>
                    <x-input-error :messages="$errors->get('vaccination_purpose')" class="mt-2" />
                </div>

                <div id="other-vaccine-wrapper" class="{{ $vaccinationPurpose === 'Others' ? '' : 'hidden' }}">
                    <x-input-label for="other_vaccine" :value="__('Other Vaccine Name')" />
                    <x-text-input id="other_vaccine" name="other_vaccine" class="mt-1 block w-full" :value="$otherVaccine" />
                    <x-input-error :messages="$errors->get('other_vaccine')" class="mt-2" />
                </div>
            </div>
        </div>

        <div id="checkup-fields" class="md:col-span-2 {{ $isCheckup ? '' : 'hidden' }}">
            <x-input-label for="checkup_purpose" :value="__('Checkup Reason')" />
            <x-text-input
                id="checkup_purpose"
                name="checkup_purpose"
                class="mt-1 block w-full"
                :value="$checkupPurpose"
                placeholder="Enter reason for checkup (e.g., skin allergy, follow-up visit)"
            />
            <x-input-error :messages="$errors->get('checkup_purpose')" class="mt-2" />
        </div>

        <div id="grooming-fields" class="contents {{ $isGrooming ? '' : 'hidden' }}">
            <div>
                <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                <x-text-input id="preferred_time" name="preferred_time" type="time" class="mt-1 block w-full" :value="$preferredTime" />
                <x-input-error :messages="$errors->get('preferred_time')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="grooming_service_type" :value="__('Grooming Service Type')" />
                <select id="grooming_service_type" name="grooming_service_type" class="mt-1 block w-full rounded-md border-slate-300">
                    <option value="">Select Service</option>
                    @foreach($groomingServices as $serviceKey => $serviceLabel)
                        <option value="{{ $serviceKey }}" @selected(old('grooming_service_type', $appointment?->grooming_service_type) === $serviceKey)>
                            {{ $serviceLabel }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('grooming_service_type')" class="mt-2" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="notes" :value="__('Notes (Optional)')" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-slate-300">{{ old('notes', $appointment?->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
        </div>

        @if($isClient)
            @if($appointment)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @else
                <input type="hidden" name="status" value="{{ \App\Models\Appointment::STATUS_PENDING }}">
            @endif
        @else
            <div>
                <x-input-label for="status" :value="__('Current Status')" />
                <div id="status" class="mt-1 flex min-h-10 items-center rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-700">
                    {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                </div>
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <p class="mt-1 text-xs text-slate-500">Use the status action menu to approve or complete an appointment.</p>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="is_emergency" :value="__('Emergency Flag')" />
                <label for="is_emergency" class="mt-1 flex flex-wrap items-center gap-2 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    <input
                        id="is_emergency"
                        name="is_emergency"
                        type="checkbox"
                        value="1"
                        class="rounded border-rose-300 text-rose-600 focus:ring-rose-500"
                        @checked($isEmergency)
                    >
                    <span>Mark as emergency appointment</span>
                </label>
                <x-input-error :messages="$errors->get('is_emergency')" class="mt-2" />
            </div>
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('appointments.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>

<script>
    (() => {
        const form = document.getElementById('appointment-form');
        if (!form) return;

        const calendar = form.querySelector('#appointment-calendar');
        const appointmentDateField = form.querySelector('#appointment_date');
        if (calendar && appointmentDateField) {
            const monthLabel = calendar.querySelector('#appointment-calendar-month');
            const daysGrid = calendar.querySelector('#appointment-calendar-days');
            const selectionLabel = calendar.querySelector('#appointment-calendar-selection');
            const statusLabel = calendar.querySelector('#appointment-calendar-status');
            const appointmentId = calendar.dataset.appointmentId;
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const parseDate = (value) => {
                if (!value) return null;
                const [year, month, day] = value.split('-').map(Number);
                return new Date(year, month - 1, day);
            };

            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            const initialDate = parseDate(appointmentDateField.value);
            let visibleMonth = new Date(
                initialDate?.getFullYear() ?? today.getFullYear(),
                initialDate?.getMonth() ?? today.getMonth(),
                1
            );
            let bookedByDate = {};
            let dailyLimit = Number(calendar.dataset.limit);
            let availabilityLoaded = false;
            let availabilityRequest = null;
            let requestSequence = 0;

            const updateSelectionLabel = () => {
                const selectedDate = parseDate(appointmentDateField.value);
                if (!selectedDate) {
                    selectionLabel.textContent = 'Choose an available date to continue.';
                    return;
                }

                if (!availabilityLoaded
                    || selectedDate.getMonth() !== visibleMonth.getMonth()
                    || selectedDate.getFullYear() !== visibleMonth.getFullYear()) {
                    selectionLabel.textContent = `Selected: ${selectedDate.toLocaleDateString(undefined, {
                        weekday: 'long',
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric',
                    })}`;
                    return;
                }

                const dateKey = formatDate(selectedDate);
                const remaining = Math.max(0, dailyLimit - (bookedByDate[dateKey] ?? 0));
                selectionLabel.textContent = `Selected: ${selectedDate.toLocaleDateString(undefined, {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric',
                })} · ${remaining} ${remaining === 1 ? 'slot' : 'slots'} remaining`;
            };

            const renderDays = () => {
                monthLabel.textContent = visibleMonth.toLocaleDateString(undefined, {
                    month: 'long',
                    year: 'numeric',
                });
                daysGrid.replaceChildren();

                const firstDay = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), 1);
                const daysInMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 0).getDate();
                const totalCells = Math.ceil((firstDay.getDay() + daysInMonth) / 7) * 7;

                for (let cell = 0; cell < totalCells; cell += 1) {
                    const dayNumber = cell - firstDay.getDay() + 1;
                    const isCurrentMonth = dayNumber > 0 && dayNumber <= daysInMonth;
                    const date = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), dayNumber);
                    const dateKey = formatDate(date);
                    const booked = bookedByDate[dateKey] ?? 0;
                    const remaining = Math.max(0, dailyLimit - booked);
                    const isSelected = dateKey === appointmentDateField.value;
                    const isPast = date < today && dateKey !== calendar.dataset.selectedDate;
                    const isAvailable = isCurrentMonth && !isPast && remaining > 0;
                    const button = document.createElement('button');

                    button.type = 'button';
                    button.disabled = !isAvailable && !isSelected;
                    button.setAttribute('aria-label', isCurrentMonth
                        ? `${date.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' })}, ${remaining} ${remaining === 1 ? 'slot' : 'slots'} remaining`
                        : 'Outside current month');
                    button.className = [
                        'flex min-h-16 flex-col items-start justify-between border-b border-r border-slate-200 p-1.5 text-left transition sm:min-h-20 sm:p-2',
                        isCurrentMonth ? 'bg-white' : 'bg-slate-50 text-slate-300',
                        isAvailable ? 'cursor-pointer hover:bg-indigo-50' : '',
                        isPast || (isCurrentMonth && remaining === 0 && !isSelected) ? 'cursor-not-allowed bg-slate-50 text-slate-400' : '',
                        isSelected ? 'bg-indigo-50 ring-2 ring-inset ring-indigo-500' : '',
                    ].filter(Boolean).join(' ');

                    if (isCurrentMonth) {
                        const dayLabel = document.createElement('span');
                        dayLabel.className = `text-xs font-semibold sm:text-sm ${isSelected ? 'text-indigo-700' : 'text-slate-700'}`;
                        dayLabel.textContent = String(dayNumber);
                        button.append(dayLabel);

                        const availabilityLabel = document.createElement('span');
                        availabilityLabel.className = `text-[0.6rem] font-medium leading-3 sm:text-[0.7rem] ${remaining > 0 ? 'text-slate-500' : 'text-rose-600'}`;
                        availabilityLabel.textContent = remaining > 0
                            ? `${remaining} ${remaining === 1 ? 'slot' : 'slots'} left`
                            : 'Full';
                        button.append(availabilityLabel);

                        if (isAvailable || isSelected) {
                            button.addEventListener('click', () => {
                                appointmentDateField.value = dateKey;
                                calendar.dataset.selectedDate = dateKey;
                                statusLabel.textContent = '';
                                updateSelectionLabel();
                                renderDays();
                            });
                        }
                    }

                    daysGrid.append(button);
                }

                updateSelectionLabel();
            };

            const loadAvailability = async () => {
                const sequence = ++requestSequence;
                availabilityRequest?.abort();
                availabilityRequest = new AbortController();
                availabilityLoaded = false;
                statusLabel.textContent = 'Loading availability…';
                daysGrid.replaceChildren();
                updateSelectionLabel();

                const month = `${visibleMonth.getFullYear()}-${String(visibleMonth.getMonth() + 1).padStart(2, '0')}`;
                const url = new URL(calendar.dataset.availabilityUrl, window.location.href);
                url.searchParams.set('month', month);
                if (appointmentId) url.searchParams.set('appointment_id', appointmentId);

                try {
                    const response = await fetch(url, {
                        headers: { Accept: 'application/json' },
                        signal: availabilityRequest.signal,
                    });
                    if (!response.ok) {
                        throw new Error('Availability could not be loaded. Please try again.');
                    }

                    const data = await response.json();
                    if (sequence !== requestSequence) return;

                    bookedByDate = data.booked ?? {};
                    dailyLimit = Number(data.limit) || dailyLimit;
                    availabilityLoaded = true;
                    statusLabel.textContent = '';
                    renderDays();
                } catch (error) {
                    if (error.name === 'AbortError' || sequence !== requestSequence) return;
                    daysGrid.replaceChildren();
                    statusLabel.textContent = error.message || 'Availability could not be loaded. Please try again.';
                    updateSelectionLabel();
                }
            };

            calendar.querySelector('[data-calendar-previous]')?.addEventListener('click', () => {
                visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() - 1, 1);
                loadAvailability();
            });
            calendar.querySelector('[data-calendar-next]')?.addEventListener('click', () => {
                visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 1);
                loadAvailability();
            });
            calendar.querySelector('[data-calendar-today]')?.addEventListener('click', () => {
                visibleMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                loadAvailability();
            });

            form.addEventListener('submit', (event) => {
                if (!appointmentDateField.value) {
                    event.preventDefault();
                    statusLabel.textContent = 'Please select an available appointment date.';
                    calendar.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            loadAvailability();
        }

        const typeSelect = form.querySelector('#appointment_type');
        const hiddenTypeInput = form.querySelector('input[name="type"]');
        const appointmentDateLabel = form.querySelector('#appointment_date_label');
        const vaccinationFields = form.querySelector('#vaccination-fields');
        const checkupFields = form.querySelector('#checkup-fields');
        const groomingFields = form.querySelector('#grooming-fields');
        const vaccinationPurposeField = form.querySelector('#vaccination_purpose');
        const otherVaccineWrapper = form.querySelector('#other-vaccine-wrapper');
        const otherVaccineField = form.querySelector('#other_vaccine');
        const checkupPurposeField = form.querySelector('#checkup_purpose');
        const preferredTimeField = form.querySelector('#preferred_time');
        const groomingServiceField = form.querySelector('#grooming_service_type');

        const getType = () => {
            if (typeSelect) return typeSelect.value;
            if (hiddenTypeInput) return hiddenTypeInput.value;

            return '{{ \App\Models\Appointment::TYPE_VACCINATION }}';
        };

        const toggle = (el, show) => {
            if (!el) return;
            el.classList.toggle('hidden', !show);
        };

        const setRequired = (el, required) => {
            if (!el) return;
            if (required) {
                el.setAttribute('required', 'required');
                return;
            }

            el.removeAttribute('required');
        };

        const syncFields = () => {
            const type = getType();
            const isVaccinationType = type === '{{ \App\Models\Appointment::TYPE_VACCINATION }}';
            const isCheckupType = type === '{{ \App\Models\Appointment::TYPE_CHECKUP }}';
            const isGroomingType = type === '{{ \App\Models\Appointment::TYPE_GROOMING }}';

            toggle(vaccinationFields, isVaccinationType);
            toggle(checkupFields, isCheckupType);
            toggle(groomingFields, isGroomingType);

            if (appointmentDateLabel) {
                appointmentDateLabel.textContent = isGroomingType ? 'Preferred Date' : 'Appointment Date';
            }

            setRequired(vaccinationPurposeField, isVaccinationType);
            setRequired(checkupPurposeField, isCheckupType);
            setRequired(preferredTimeField, isGroomingType);
            setRequired(groomingServiceField, isGroomingType);

            const showOtherVaccine = isVaccinationType && vaccinationPurposeField?.value === 'Others';
            toggle(otherVaccineWrapper, showOtherVaccine);
            setRequired(otherVaccineField, showOtherVaccine);
        };

        typeSelect?.addEventListener('change', syncFields);
        vaccinationPurposeField?.addEventListener('change', syncFields);

        syncFields();
    })();
</script>
