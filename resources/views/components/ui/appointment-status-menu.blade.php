@props(['appointment'])

@if($appointment->status === \App\Models\Appointment::STATUS_PENDING && ! $appointment->accepted_at)
    <form method="POST" action="{{ route('appointments.approve', $appointment) }}">
        @csrf
        @method('PATCH')
        <x-ui.button type="submit">Accept</x-ui.button>
    </form>
@elseif(
    $appointment->status === \App\Models\Appointment::STATUS_APPROVED
    || ($appointment->status === \App\Models\Appointment::STATUS_PENDING && $appointment->accepted_at)
)
    <form method="POST" action="{{ route('appointments.complete', $appointment) }}">
        @csrf
        @method('PATCH')
        <x-ui.button type="submit">Complete</x-ui.button>
    </form>
@endif
