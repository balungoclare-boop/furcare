<x-app-layout>
    @php
        $user = auth()->user();
        $isStaffOrAdmin = $user?->isStaffOrAdmin() ?? false;
        $speciesOptions = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Other'];
        $oldSpecies = old('species', '');
        $speciesChoice = in_array($oldSpecies, $speciesOptions, true) ? $oldSpecies : ($oldSpecies === '' ? 'Dog' : 'Other');
        $otherSpecies = $speciesChoice === 'Other' && $oldSpecies !== 'Other' ? $oldSpecies : '';
        $clientOptions = $clients->map(fn ($client) => [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
        ])->values();
        $hasHealthErrors = $errors->hasAny(['allergies', 'notes', 'microchip_number']);
    @endphp

    <div
        class="mx-auto max-w-3xl pb-10"
        x-data="{
            isStaff: @js($isStaffOrAdmin),
            speciesChoice: @js($speciesChoice),
            otherSpecies: @js($otherSpecies),
            petName: @js(old('pet_name', '')),
            breed: @js(old('breed', '')),
            approximateAge: @js((bool) old('age_is_approximate', false)),
            dateOfBirth: @js(old('date_of_birth', '')),
            ageValue: @js(old('age_value', '')),
            ageType: @js(old('age_type', 'year')),
            selectedSex: @js(old('sex', 'unknown')),
            neutered: @js((bool) old('is_neutered', false)),
            owners: @js($clientOptions),
            ownerId: @js((string) old('owner_user_id', '')),
            ownerSearch: '',
            ownerOpen: false,
            ownerError: false,
            clientErrors: {},
            breeds: {
                Dog: ['Labrador Retriever', 'Golden Retriever', 'German Shepherd', 'Poodle', 'Beagle', 'Shih Tzu', 'Mixed / Unknown'],
                Cat: ['Domestic Shorthair', 'Domestic Longhair', 'Siamese', 'Persian', 'Maine Coon', 'Mixed / Unknown'],
                Bird: ['Parakeet', 'Cockatiel', 'Parrot', 'Canary', 'Mixed / Unknown'],
                Rabbit: ['Dutch', 'Holland Lop', 'Lionhead', 'Rex', 'Mixed / Unknown'],
                Other: ['Mixed / Unknown']
            },
            get speciesValue() {
                return this.speciesChoice === 'Other' ? this.otherSpecies : this.speciesChoice;
            },
            get filteredOwners() {
                const query = this.ownerSearch.trim().toLowerCase();
                return this.owners.filter(owner => !query || owner.name.toLowerCase().includes(query) || owner.email.toLowerCase().includes(query));
            },
            get selectedOwner() {
                return this.owners.find(owner => String(owner.id) === this.ownerId);
            },
            init() {
                if (this.selectedOwner) {
                    this.ownerSearch = `${this.selectedOwner.name} (${this.selectedOwner.email})`;
                }
            },
            get previewAge() {
                if (this.approximateAge) {
                    if (this.ageValue === '') return 'Approximate age not provided';
                    return `${this.ageValue} ${this.ageType}${Number(this.ageValue) === 1 ? '' : 's'}`;
                }
                if (!this.dateOfBirth) return 'Age not provided';
                const birth = new Date(`${this.dateOfBirth}T00:00:00`);
                const today = new Date();
                let years = today.getFullYear() - birth.getFullYear();
                const beforeBirthday = today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate());
                if (beforeBirthday) years--;
                if (years > 0) return `${years} year${years === 1 ? '' : 's'}`;
                let months = (today.getFullYear() - birth.getFullYear()) * 12 + today.getMonth() - birth.getMonth();
                if (today.getDate() < birth.getDate()) months--;
                return `${Math.max(0, months)} month${months === 1 ? '' : 's'}`;
            },
            chooseOwner(owner) {
                this.ownerId = String(owner.id);
                this.ownerSearch = `${owner.name} (${owner.email})`;
                this.ownerOpen = false;
                this.ownerError = false;
            },
            validateField(field, value) {
                this.clientErrors[field] = !String(value || '').trim();
            },
            guardSubmit(event) {
                this.ownerError = this.isStaff && !this.ownerId;
                if (this.ownerError || (this.speciesChoice === 'Other' && !this.otherSpecies.trim())) {
                    event.preventDefault();
                }
            }
        }"
    >
        <header class="mb-6">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">Patient registration</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Add a pet patient</h1>
            <p class="mt-1 text-sm text-slate-500">Create a profile for your clinic's new patient.</p>
        </header>

        <form method="POST" action="{{ route('pets.store') }}" class="overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-xl shadow-indigo-950/5" @submit="guardSubmit($event)">
            @csrf
            <input type="hidden" name="age_is_approximate" :value="approximateAge ? 1 : 0">
            <input type="hidden" name="species" :value="speciesValue">
            @if(! $isStaffOrAdmin)
                <input type="hidden" name="owner_user_id" value="{{ $user->id }}">
            @else
                <input type="hidden" name="owner_user_id" :value="ownerId">
            @endif

            <div class="space-y-8 p-5 sm:p-8">
                <section aria-labelledby="owner-heading">
                    @if($isStaffOrAdmin)
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div class="min-w-[16rem] flex-1">
                                <label id="owner-heading" for="owner_search" class="block text-sm font-semibold text-slate-700">
                                    Owner <span class="text-rose-600" aria-hidden="true">*</span>
                                </label>
                                <div class="relative mt-1.5">
                                    <input
                                        id="owner_search"
                                        type="search"
                                        x-model="ownerSearch"
                                        @focus="ownerOpen = true"
                                        @input="ownerId = ''; ownerOpen = true; ownerError = false"
                                        @keydown.escape="ownerOpen = false"
                                        @blur="setTimeout(() => { ownerOpen = false; ownerError = isStaff && !ownerId; }, 150)"
                                        :required="isStaff && !ownerId"
                                        autocomplete="off"
                                        placeholder="Search owners by name or email"
                                        class="block w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100"
                                    >
                                    <div x-cloak x-show="ownerOpen" class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                                        <template x-for="owner in filteredOwners" :key="owner.id">
                                            <button type="button" @mousedown.prevent="chooseOwner(owner)" class="flex w-full flex-col rounded-lg px-3 py-2 text-left hover:bg-indigo-50">
                                                <span class="text-sm font-semibold text-slate-800" x-text="owner.name"></span>
                                                <span class="text-xs text-slate-500" x-text="owner.email"></span>
                                            </button>
                                        </template>
                                        <p x-show="filteredOwners.length === 0" class="px-3 py-2 text-sm text-slate-500">No owners found.</p>
                                    </div>
                                </div>
                                @error('owner_user_id')
                                    <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                                <p x-cloak x-show="ownerError" class="mt-1.5 text-sm text-rose-600">Choose an owner before saving.</p>
                            </div>
                            @if($user->isAdmin())
                                <a href="{{ route('admin.users.create') }}" class="pb-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">+ Add new owner</a>
                            @else
                                <p class="pb-2 text-xs text-slate-500">New owner accounts are created by an administrator.</p>
                            @endif
                        </div>
                    @else
                        <p class="rounded-xl border border-indigo-100 bg-indigo-50/70 px-4 py-3 text-sm text-indigo-900">
                            <span class="font-semibold">Registering for:</span> {{ $user->name }}
                        </p>
                    @endif
                </section>

                <section aria-labelledby="basic-info-heading">
                    <div class="mb-4 border-b border-slate-100 pb-3">
                        <h2 id="basic-info-heading" class="text-base font-bold text-slate-900">Basic info</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Start with your pet's name and type.</p>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label for="pet_name" class="block text-sm font-semibold text-slate-700">Pet name <span class="text-rose-600" aria-hidden="true">*</span></label>
                            <input
                                id="pet_name"
                                name="pet_name"
                                type="text"
                                value="{{ old('pet_name') }}"
                                placeholder="Max"
                                maxlength="255"
                                x-model="petName"
                                required
                                @blur="validateField('pet_name', $event.target.value)"
                                @input="clientErrors.pet_name = false"
                                class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100"
                            >
                            @error('pet_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            <p x-cloak x-show="clientErrors.pet_name" class="mt-1.5 text-sm text-rose-600">Enter your pet's name.</p>
                        </div>

                        <fieldset>
                            <legend class="mb-2 block text-sm font-semibold text-slate-700">Species <span class="text-rose-600" aria-hidden="true">*</span></legend>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-5">
                                @foreach([
                                    'Dog' => '🐶',
                                    'Cat' => '🐱',
                                    'Bird' => '🐦',
                                    'Rabbit' => '🐰',
                                    'Other' => '✦',
                                ] as $species => $icon)
                                    <label class="cursor-pointer">
                                        <input class="peer sr-only" type="radio" name="species_choice" value="{{ $species }}" x-model="speciesChoice" @change="clientErrors.species = false" @blur="clientErrors.species = !speciesValue.trim()" @if($loop->first) required @endif>
                                        <span class="flex min-h-12 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-600 transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-800 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-300">
                                            <span aria-hidden="true">{{ $icon }}</span>{{ $species }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <div x-cloak x-show="speciesChoice === 'Other'" class="mt-3">
                                <label for="other_species" class="block text-sm font-medium text-slate-700">Tell us the species <span class="text-rose-600" aria-hidden="true">*</span></label>
                                <input id="other_species" type="text" x-model="otherSpecies" :required="speciesChoice === 'Other'" @blur="clientErrors.species = !speciesValue.trim()" maxlength="120" placeholder="e.g. Guinea pig" class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100">
                            </div>
                            @error('species')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            <p x-cloak x-show="speciesChoice === 'Other' && !otherSpecies.trim()" class="mt-1.5 text-sm text-rose-600">Enter the species.</p>
                        </fieldset>

                        <div>
                            <label for="breed" class="block text-sm font-semibold text-slate-700">Breed <span class="text-slate-400">(optional)</span></label>
                            <input
                                id="breed"
                                name="breed"
                                type="text"
                                list="breed-options"
                                x-model="breed"
                                maxlength="150"
                                placeholder="Golden Retriever"
                                class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100"
                            >
                            <datalist id="breed-options">
                                <template x-for="option in (breeds[speciesChoice] || []).filter(item => item.toLowerCase().includes(breed.toLowerCase()))" :key="option">
                                    <option :value="option"></option>
                                </template>
                            </datalist>
                            @error('breed')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section aria-labelledby="details-heading">
                    <div class="mb-4 border-b border-slate-100 pb-3">
                        <h2 id="details-heading" class="text-base font-bold text-slate-900">Details</h2>
                        <p class="mt-0.5 text-sm text-slate-500">A few more details help us care for your pet.</p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <fieldset class="sm:col-span-2">
                            <legend class="mb-2 block text-sm font-semibold text-slate-700">Sex <span class="text-rose-600" aria-hidden="true">*</span></legend>
                            <div class="inline-flex max-w-full rounded-xl border border-slate-200 bg-slate-50 p-1">
                                @foreach(['male' => 'Male', 'female' => 'Female', 'unknown' => 'Unknown'] as $value => $label)
                                    <label class="cursor-pointer">
                                        <input class="peer sr-only" type="radio" name="sex" value="{{ $value }}" x-model="selectedSex" @change="clientErrors.sex = false" @blur="clientErrors.sex = !selectedSex" @if($loop->first) required @endif>
                                        <span class="block rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-indigo-800 peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('sex')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            <p x-cloak x-show="clientErrors.sex" class="mt-1.5 text-sm text-rose-600">Choose a sex or select unknown.</p>
                        </fieldset>

                        <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 px-4 py-3 sm:col-span-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Neutered / spayed</p>
                                <p class="text-xs text-slate-500">Select if your pet has been neutered or spayed.</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" name="is_neutered" value="1" x-model="neutered" class="peer sr-only">
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-indigo-600 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-300 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
                                <span class="sr-only">Neutered or spayed</span>
                            </label>
                        </div>

                        <fieldset class="sm:col-span-2">
                            <legend class="mb-2 block text-sm font-semibold text-slate-700">Age <span class="text-rose-600" aria-hidden="true">*</span></legend>
                            <div x-cloak x-show="!approximateAge">
                                <label for="date_of_birth" class="block text-xs font-medium text-slate-600">Date of birth</label>
                                <input
                                    id="date_of_birth"
                                    name="date_of_birth"
                                    type="date"
                                    x-model="dateOfBirth"
                                    :disabled="approximateAge"
                                    :required="!approximateAge"
                                    max="{{ now()->toDateString() }}"
                                    @blur="validateField('date_of_birth', $event.target.value)"
                                    @input="clientErrors.date_of_birth = false"
                                    class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100 sm:max-w-xs"
                                >
                            </div>
                            <div x-cloak x-show="approximateAge" class="grid gap-3 sm:grid-cols-[1fr_10rem] sm:max-w-lg">
                                <div>
                                    <label for="age_value" class="block text-xs font-medium text-slate-600">Approximate age</label>
                                    <input
                                        id="age_value"
                                        name="age_value"
                                        type="number"
                                        min="0"
                                        max="240"
                                        x-model="ageValue"
                                        :disabled="!approximateAge"
                                        :required="approximateAge"
                                        @blur="validateField('age_value', $event.target.value)"
                                        @input="clientErrors.age_value = false"
                                        placeholder="e.g. 3"
                                        class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100"
                                    >
                                </div>
                                <div>
                                    <label for="age_type" class="block text-xs font-medium text-slate-600">Age unit</label>
                                    <select id="age_type" name="age_type" x-model="ageType" :disabled="!approximateAge" :required="approximateAge" class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100">
                                        <option value="year">Year(s)</option>
                                        <option value="month">Month(s)</option>
                                    </select>
                                </div>
                            </div>
                            <label class="mt-3 inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" x-model="approximateAge" @change="clientErrors.date_of_birth = false; clientErrors.age_value = false" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>I only know the approximate age</span>
                            </label>
                            @error('date_of_birth')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            @error('age_value')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            @error('age_type')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            <p x-cloak x-show="clientErrors.date_of_birth && !approximateAge" class="mt-1.5 text-sm text-rose-600">Enter a date of birth or choose approximate age.</p>
                            <p x-cloak x-show="clientErrors.age_value && approximateAge" class="mt-1.5 text-sm text-rose-600">Enter the approximate age.</p>
                        </fieldset>

                        <div>
                            <label for="weight" class="block text-sm font-semibold text-slate-700">Weight <span class="text-slate-400">(optional)</span></label>
                            <div class="relative mt-1.5">
                                <input id="weight" name="weight" type="number" step="0.01" min="0" max="9999.99" value="{{ old('weight') }}" placeholder="e.g. 12.5" class="block w-full rounded-xl border-slate-200 pr-12 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100">
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-sm text-slate-400">kg</span>
                            </div>
                            @error('weight')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-semibold text-slate-700">Color / markings <span class="text-slate-400">(optional)</span></label>
                            <input id="color" name="color" type="text" value="{{ old('color') }}" maxlength="150" placeholder="Black and white" class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100">
                            @error('color')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section>
                    <details class="group rounded-xl border border-slate-200" @if($hasHealthErrors) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3.5">
                            <span>
                                <span class="block text-sm font-bold text-slate-800">Health notes <span class="font-normal text-slate-400">(optional)</span></span>
                                <span class="mt-0.5 block text-xs text-slate-500">Allergies, existing conditions, or microchip details.</span>
                            </span>
                            <span aria-hidden="true" class="text-indigo-600 transition group-open:rotate-180">⌄</span>
                        </summary>
                        <div class="grid gap-4 border-t border-slate-100 p-4 sm:grid-cols-2">
                            <div>
                                <label for="allergies" class="block text-sm font-semibold text-slate-700">Allergies</label>
                                <textarea id="allergies" name="allergies" rows="3" maxlength="5000" placeholder="List known allergies" class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100">{{ old('allergies') }}</textarea>
                                @error('allergies')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-semibold text-slate-700">Existing conditions</label>
                                <textarea id="notes" name="notes" rows="3" maxlength="5000" placeholder="Share any known conditions or care notes" class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100">{{ old('notes') }}</textarea>
                                @error('notes')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="microchip_number" class="block text-sm font-semibold text-slate-700">Microchip number</label>
                                <input id="microchip_number" name="microchip_number" type="text" value="{{ old('microchip_number') }}" maxlength="100" placeholder="Enter microchip number" class="mt-1.5 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100 sm:max-w-md">
                                @error('microchip_number')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </details>
                </section>
            </div>

            <aside aria-label="Patient card preview" class="mx-5 mb-5 rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-teal-50 p-4 sm:mx-8">
                <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-indigo-600">Patient card preview</p>
                <div class="mt-2 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-2xl shadow-sm" aria-hidden="true" x-text="{ Dog: '🐶', Cat: '🐱', Bird: '🐦', Rabbit: '🐰', Other: '🐾' }[speciesChoice] || '🐾'"></span>
                    <div class="min-w-0">
                        <p class="truncate font-bold text-slate-900" x-text="petName || 'Pet name'"></p>
                        <p class="truncate text-sm text-slate-600"><span x-text="speciesValue || 'Species'"></span><span x-show="breed"> · </span><span x-text="breed"></span><span x-show="breed"> · </span><span x-text="previewAge"></span></p>
                    </div>
                </div>
            </aside>

            <footer class="sticky bottom-0 z-10 flex flex-col-reverse gap-3 border-t border-indigo-100 bg-white/95 px-5 py-4 backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <a href="{{ route('pets.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900">Cancel</a>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="submit" name="submit_action" value="save_add_another" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-indigo-200 bg-white px-4 text-sm font-semibold text-indigo-800 shadow-sm hover:bg-indigo-50">Save and add another</button>
                    <button type="submit" name="submit_action" value="save_book_appointment" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-indigo-200 bg-white px-4 text-sm font-semibold text-indigo-800 shadow-sm hover:bg-indigo-50">Save and book appointment</button>
                    <button type="submit" name="submit_action" value="save" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-indigo-700 px-6 text-sm font-bold text-white shadow-md shadow-indigo-200 hover:bg-indigo-800">Save</button>
                </div>
            </footer>
        </form>
    </div>
</x-app-layout>
