@props([
    'vaccination' => null,
    'action',
    'method' => 'POST',
])

<form id="vaccination-form" method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div>
        <x-input-label for="pet_id" :value="__('Link to Registered Pet (optional)')" />
        <select id="pet_id" name="pet_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Manual / walk-in record</option>
            @foreach($pets as $pet)
                <option
                    value="{{ $pet->id }}"
                    data-pet-name="{{ $pet->pet_name }}"
                    data-owner-name="{{ $pet->owner_name }}"
                    data-contact-number="{{ $pet->contact_number }}"
                    @selected((string) old('pet_id', $vaccination?->pet_id) === (string) $pet->id)
                >
                    {{ $pet->pet_name }} — {{ $pet->owner_name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Linking the record adds it to the pet's medical history and the owner's reminders.</p>
        <x-input-error :messages="$errors->get('pet_id')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="pet_name" :value="__('Pet Name')" />
            <x-text-input
                id="pet_name"
                name="pet_name"
                class="mt-1 block w-full"
                :value="old('pet_name', $vaccination?->pet_name ?? $vaccination?->pet?->pet_name)"
                placeholder="Enter Pet Name"
                required
            />
            <x-input-error :messages="$errors->get('pet_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="vaccine_name" :value="__('Vaccine Name')" />
            <x-text-input id="vaccine_name" name="vaccine_name" class="mt-1 block w-full" :value="old('vaccine_name', $vaccination?->vaccine_name)" required />
            <x-input-error :messages="$errors->get('vaccine_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="owner_name" :value="__('Owner Name')" />
            <x-text-input
                id="owner_name"
                name="owner_name"
                class="mt-1 block w-full"
                :value="old('owner_name', $vaccination?->owner_name ?? $vaccination?->pet?->owner_name)"
                placeholder="Enter Owner Name"
            />
            <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="contact_number" :value="__('Contact Number')" />
            <x-text-input
                id="contact_number"
                name="contact_number"
                class="mt-1 block w-full"
                :value="old('contact_number', $vaccination?->contact_number ?? $vaccination?->pet?->contact_number)"
                placeholder="Enter Contact Number"
            />
            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="date_given" :value="__('Date Given')" />
            <x-text-input id="date_given" name="date_given" type="date" class="mt-1 block w-full" :value="old('date_given', optional($vaccination?->date_given)->toDateString())" required />
            <x-input-error :messages="$errors->get('date_given')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="next_due_date" :value="__('Next Due Date')" />
            <x-text-input id="next_due_date" name="next_due_date" type="date" class="mt-1 block w-full bg-slate-100" :value="old('next_due_date', optional($vaccination?->next_due_date)->toDateString())" readonly />
            <p class="mt-1 text-xs text-slate-500">Auto-calculated based on vaccine type and date given.</p>
            <x-input-error :messages="$errors->get('next_due_date')" class="mt-2" />
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('vaccinations.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>

<script>
    (() => {
        const form = document.getElementById('vaccination-form');
        if (!form) return;

        const dateGivenField = form.querySelector('#date_given');
        const nextDueField = form.querySelector('#next_due_date');
        const petField = form.querySelector('#pet_id');
        const petNameField = form.querySelector('#pet_name');
        const ownerNameField = form.querySelector('#owner_name');
        const contactNumberField = form.querySelector('#contact_number');

        const syncPetDetails = () => {
            const selectedPet = petField?.selectedOptions[0];
            if (!selectedPet?.value) {
                return;
            }

            if (petNameField) petNameField.value = selectedPet.dataset.petName || '';
            if (ownerNameField) ownerNameField.value = selectedPet.dataset.ownerName || '';
            if (contactNumberField) contactNumberField.value = selectedPet.dataset.contactNumber || '';
        };

        const syncNextDueDate = () => {
            if (!dateGivenField || !nextDueField || !dateGivenField.value) {
                return;
            }

            const baseDate = new Date(dateGivenField.value + 'T00:00:00');
            if (Number.isNaN(baseDate.getTime())) {
                return;
            }

            const nextDate = new Date(baseDate);
            nextDate.setFullYear(nextDate.getFullYear() + 1);
            nextDueField.value = nextDate.toISOString().slice(0, 10);
        };

        dateGivenField?.addEventListener('change', syncNextDueDate);
        petField?.addEventListener('change', syncPetDetails);
        syncPetDetails();
        syncNextDueDate();
    })();
</script>
