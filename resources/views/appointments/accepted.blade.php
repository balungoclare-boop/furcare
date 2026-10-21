<x-app-layout>
    @php
        $isClient = auth()->user()?->isClient();
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Accepted Appointments</h1>
            <p class="text-sm text-slate-500">
                {{ $isClient ? 'Appointments confirmed by the clinic.' : 'Appointments accepted and ready to be completed.' }}
            </p>
        </div>
        <x-ui.button :href="route('appointments.index')" variant="secondary">Back to Appointments</x-ui.button>
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('appointments.accepted') }}" class="mb-5 flex flex-wrap items-end gap-2">
            <div class="min-w-[16rem] flex-1">
                <label for="accepted-appointment-search" class="sr-only">Search accepted appointments</label>
                <x-text-input
                    id="accepted-appointment-search"
                    name="search"
                    type="search"
                    class="block w-full"
                    :value="request('search')"
                    placeholder="Search pet, owner, purpose, type, or date"
                />
            </div>
            <x-ui.button type="submit">Search</x-ui.button>
            @if(request()->filled('search'))
                <x-ui.button :href="route('appointments.accepted')" variant="secondary">Clear</x-ui.button>
            @endif
        </form>

        @if($appointments->isEmpty())
            <x-ui.empty-state
                :title="request()->filled('search') ? 'No matching accepted appointments found.' : 'No accepted appointments yet.'"
                :description="request()->filled('search') ? 'Try another pet name, owner, purpose, type, or date.' : 'Accepted appointment requests will appear here.'"
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
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($appointments as $appointment)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $appointment->display_pet_name }}</td>
                                <td class="px-4 py-3">{{ $appointment->type_label }}</td>
                                <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $appointment->display_purpose }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.appointment-status-badge :appointment="$appointment" :label="'Accepted'" />
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    <div class="inline-flex min-w-max flex-nowrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary" class="px-3 text-xs">Reschedule</x-ui.button>
                                        @if(! $isClient)
                                            <x-ui.appointment-status-menu :appointment="$appointment" />
                                            <form class="inline-flex shrink-0" method="POST" action="{{ route('appointments.cancel', $appointment) }}" x-on:submit.prevent="$dispatch('confirm-form', { form: $el, message: 'Cancel this appointment?' })">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button variant="danger" class="px-3 text-xs">Cancel</x-ui.button>
                                            </form>
                                        @endif
                                    </div>
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
