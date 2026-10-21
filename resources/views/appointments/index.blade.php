<x-app-layout>
    @php
        $isClient = auth()->user()?->isClient();
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Appointment Requests</h1>
            <p class="text-sm text-slate-500">
                {{ $isClient ? 'Track your appointment requests and clinic confirmations.' : 'Track and manage clinic schedules.' }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('appointments.accepted')" variant="secondary">Accepted Appointments</x-ui.button>
            @if(! $isClient)
                <x-ui.button :href="route('appointments.completed')" variant="secondary">Completed Appointments</x-ui.button>
            @endif
            <x-ui.button :href="route('appointments.create')">{{ $isClient ? 'Request Appointment' : 'Add Appointment' }}</x-ui.button>
            @if($isClient)
                <x-ui.button :href="route('appointments.grooming.create')" variant="secondary">Request Grooming</x-ui.button>
            @endif
        </div>
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('appointments.index') }}" class="mb-5 flex flex-wrap items-end gap-2">
            <div class="min-w-[16rem] flex-1">
                <label for="appointment-search" class="sr-only">Search appointments</label>
                <x-text-input
                    id="appointment-search"
                    name="search"
                    type="search"
                    class="block w-full"
                    :value="request('search')"
                    placeholder="Search pet, owner, purpose, type, or date"
                />
            </div>
            <x-ui.button type="submit">Search</x-ui.button>
            @if(request()->filled('search'))
                <x-ui.button :href="route('appointments.index')" variant="secondary">Clear</x-ui.button>
            @endif
        </form>

        @if($appointments->isEmpty())
            <x-ui.empty-state
                :title="request()->filled('search') ? 'No matching appointments found.' : 'No appointment requests found.'"
                :description="request()->filled('search') ? 'Try another pet name, owner, purpose, type, or date.' : 'New appointment requests will appear here for review.'"
            >
                <x-ui.button :href="route('appointments.create')">Create Appointment</x-ui.button>
            </x-ui.empty-state>
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
                                    <div class="flex flex-col items-start gap-1">
                                        @if($appointment->is_emergency)
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">
                                                Emergency
                                            </span>
                                        @endif
                                        <x-ui.appointment-status-badge :appointment="$appointment" />
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    <div class="inline-flex min-w-max flex-nowrap items-center justify-end gap-2">
                                        @if($isClient)
                                            <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary" class="px-3 text-xs">Reschedule</x-ui.button>
                                        @else
                                            <x-ui.appointment-status-menu :appointment="$appointment" />
                                            <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary" class="px-3 text-xs">Reschedule</x-ui.button>
                                        @endif
                                        @if($isClient && $appointment->status === \App\Models\Appointment::STATUS_PENDING)
                                            <form class="inline-flex shrink-0" method="POST" action="{{ route('appointments.cancel', $appointment) }}" x-on:submit.prevent="$dispatch('confirm-form', { form: $el, message: 'Are you sure you want to cancel this appointment?' })">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-xl bg-rose-100 px-3 text-xs font-semibold text-rose-800 transition hover:bg-rose-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 sm:text-sm"
                                                >
                                                    Cancel
                                                </button>
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
