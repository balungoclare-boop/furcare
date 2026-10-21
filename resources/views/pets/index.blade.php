<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pets</h1>
            <p class="text-sm text-slate-500">Manage pet profiles and clinical ownership records.</p>
        </div>
        <x-ui.button :href="route('pets.create')">Add Pet</x-ui.button>
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('pets.index') }}" class="mb-5 flex flex-wrap items-end gap-2">
            <div class="min-w-[16rem] flex-1">
                <label for="pet-search" class="sr-only">Search pets</label>
                <x-text-input
                    id="pet-search"
                    name="search"
                    type="search"
                    class="block w-full"
                    :value="request('search')"
                    placeholder="Search pet, owner, species, or breed"
                />
            </div>
            <x-ui.button type="submit">Search</x-ui.button>
            @if(request()->filled('search'))
                <x-ui.button :href="route('pets.index')" variant="secondary">Clear</x-ui.button>
            @endif
        </form>

        @if($pets->isEmpty())
            <x-ui.empty-state
                :title="request()->filled('search') ? 'No matching pets found.' : 'No pets available.'"
                :description="request()->filled('search') ? 'Try another pet name, owner, species, or breed.' : 'Start by registering your first patient profile.'"
            >
                <x-ui.button :href="route('pets.create')">Create Pet</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Pet Name</th>
                            <th class="px-4 py-3">Owner</th>
                            <th class="px-4 py-3">Species</th>
                            <th class="px-4 py-3">Sex</th>
                            <th class="px-4 py-3">Age</th>
                            <th class="px-4 py-3">Records</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($pets as $pet)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $pet->pet_name }}</td>
                                <td class="px-4 py-3">{{ $pet->owner_name }}</td>
                                <td class="px-4 py-3">{{ $pet->species }}{{ $pet->breed ? ' - '.$pet->breed : '' }}</td>
                                <td class="px-4 py-3">{{ $pet->sex ? ucfirst($pet->sex) : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    {{ $pet->display_age ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">
                                    {{ $pet->appointments_count }} appointments,
                                    {{ $pet->vaccinations_count }} vaccinations
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('pets.show', $pet)" variant="secondary">View</x-ui.button>
                                        <x-ui.button :href="route('pets.edit', $pet)" variant="secondary">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pets->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
