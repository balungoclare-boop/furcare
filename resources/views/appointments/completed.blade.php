<x-app-layout>
    @php
        $isClient = auth()->user()?->isClient();
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Completed Appointments</h1>
            <p class="text-sm text-slate-500">
                Archive of appointments marked as completed.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('appointments.index')" variant="secondary">Back to Appointments</x-ui.button>
            @if(! $isClient)
                <x-ui.button :href="route('appointments.create')">Add Appointment</x-ui.button>
            @endif
        </div>
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('appointments.completed') }}" class="mb-5 flex flex-wrap items-end gap-2">
            <div class="min-w-[16rem] flex-1">
                <label for="completed-appointment-search" class="sr-only">Search completed appointments</label>
                <x-text-input
                    id="completed-appointment-search"
                    name="search"
                    type="search"
                    class="block w-full"
                    :value="request('search')"
                    placeholder="Search pet, owner, purpose, type, or date"
                />
            </div>
            <x-ui.button type="submit">Search</x-ui.button>
            @if(request()->filled('search'))
                <x-ui.button :href="route('appointments.completed')" variant="secondary">Clear</x-ui.button>
            @endif
        </form>

        @if($appointments->isEmpty())
            <x-ui.empty-state
                :title="request()->filled('search') ? 'No matching completed appointments found.' : 'No completed appointments yet.'"
                :description="request()->filled('search') ? 'Try another pet name, owner, purpose, type, or date.' : 'Completed appointments will appear here once finalized.'"
            />
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Pet Name</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Purpose</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($appointments as $appointment)
                            <tr class="transition-colors hover:bg-emerald-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $appointment->display_pet_name }}</td>
                                <td class="px-4 py-3">{{ $appointment->type_label }}</td>
                                <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $appointment->display_purpose }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase text-emerald-700">
                                        Completed
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $appointments->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
