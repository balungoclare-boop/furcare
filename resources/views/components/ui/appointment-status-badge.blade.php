@props(['appointment', 'label' => null])

@php
    $displayLabel = $label ?? $appointment->status_label;
    $tone = $displayLabel === 'Accepted'
        ? 'success'
        : match ($appointment->status) {
            \App\Models\Appointment::STATUS_PENDING => 'warning',
            \App\Models\Appointment::STATUS_APPROVED,
            \App\Models\Appointment::STATUS_COMPLETED => 'success',
            \App\Models\Appointment::STATUS_CANCELLED,
            \App\Models\Appointment::STATUS_REJECTED => 'danger',
            default => 'neutral',
        };
    $icon = match ($tone) {
        'warning' => 'clock',
        'success' => 'check',
        'danger' => 'alert',
        default => null,
    };
@endphp

<x-ui.badge :tone="$tone" :icon="$icon" {{ $attributes->merge(['class' => 'uppercase']) }}>
    {{ $displayLabel }}
</x-ui.badge>
