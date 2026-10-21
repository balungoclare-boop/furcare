@props([
    'pet' => null,
    'clients' => collect(),
    'action',
    'method' => 'POST',
])

@php
    $isStaffOrAdmin = auth()->user()?->isStaffOrAdmin();
@endphp

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @if($isStaffOrAdmin)
            <div class="md:col-span-2">
                <x-input-label for="owner_user_id" :value="__('Client Account')" />
                <select id="owner_user_id" name="owner_user_id" class="mt-1 block w-full rounded-md border-slate-300">
                    <option value="">Select Client (optional)</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected((string) old('owner_user_id', $pet?->user_id) === (string) $client->id)>
                            {{ $client->name }} ({{ $client->email }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('owner_user_id')" class="mt-2" />
            </div>
        @else
            <input type="hidden" name="owner_user_id" value="{{ auth()->id() }}">
            <div class="md:col-span-2 rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-indigo-600">Owner</p>
                <p class="mt-1 text-sm font-medium text-slate-800">{{ auth()->user()?->name }}</p>
            </div>
        @endif

        <div>
            <x-input-label for="pet_name" :value="__('Pet Name')" />
            <x-text-input id="pet_name" name="pet_name" class="mt-1 block w-full" :value="old('pet_name', $pet?->pet_name)" required />
            <x-input-error :messages="$errors->get('pet_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="species" :value="__('Species')" />
            <x-text-input id="species" name="species" class="mt-1 block w-full" :value="old('species', $pet?->species)" required />
            <x-input-error :messages="$errors->get('species')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="breed" :value="__('Breed')" />
            <x-text-input id="breed" name="breed" class="mt-1 block w-full" :value="old('breed', $pet?->breed)" />
            <x-input-error :messages="$errors->get('breed')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="sex" :value="__('Sex')" />
            <select
                id="sex"
                name="sex"
                class="mt-1 block w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
                <option value="">Select Sex</option>
                <option value="male" @selected(old('sex', $pet?->sex) === 'male')>Male</option>
                <option value="female" @selected(old('sex', $pet?->sex) === 'female')>Female</option>
                <option value="unknown" @selected(old('sex', $pet?->sex) === 'unknown')>Unknown</option>
            </select>
            <x-input-error :messages="$errors->get('sex')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="age_value" :value="__('Age')" />
            <div class="mt-1 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <x-text-input
                    id="age_value"
                    name="age_value"
                    type="number"
                    min="0"
                    max="240"
                    class="block w-full sm:col-span-2"
                    :value="old('age_value', $pet?->age_value ?? $pet?->age)"
                    required
                />
                <select
                    id="age_type"
                    name="age_type"
                    class="block w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                    <option value="month" @selected(old('age_type', $pet?->age_type ?? 'year') === 'month')>Month(s)</option>
                    <option value="year" @selected(old('age_type', $pet?->age_type ?? 'year') === 'year')>Year(s)</option>
                </select>
            </div>
            <x-input-error :messages="$errors->get('age_value')" class="mt-2" />
            <x-input-error :messages="$errors->get('age_type')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="date_of_birth" :value="__('Date of Birth (optional)')" />
            <x-text-input
                id="date_of_birth"
                name="date_of_birth"
                type="date"
                max="{{ today()->toDateString() }}"
                class="mt-1 block w-full"
                :value="old('date_of_birth', $pet?->date_of_birth?->toDateString())"
            />
            <p class="mt-1 text-xs text-slate-500">Enter a date of birth to use the pet's exact age, or leave it blank to use the age above.</p>
            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('pets.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>
