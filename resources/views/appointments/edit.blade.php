<x-app-layout>
    <x-ui.card
        title="Reschedule Appointment"
        subtitle="Choose a new date or time, and update the appointment details."
    >
        @include('appointments._form', [
            'action' => route('appointments.update', $appointment),
            'method' => 'PUT',
            'appointment' => $appointment,
            'pets' => $pets,
            'appointmentType' => $appointmentType ?? ($appointment->type ?? \App\Models\Appointment::TYPE_VACCINATION),
            'allowTypeSelection' => false,
        ])
    </x-ui.card>
</x-app-layout>
