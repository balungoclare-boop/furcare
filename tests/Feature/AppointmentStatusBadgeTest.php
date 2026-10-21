<?php

use App\Models\Appointment;

test('appointment status badges use yellow for pending and green for completed', function () {
    $pendingBadge = $this->blade(
        '<x-ui.appointment-status-badge :appointment="$appointment" />',
        ['appointment' => new Appointment(['status' => Appointment::STATUS_PENDING])]
    );
    $completedBadge = $this->blade(
        '<x-ui.appointment-status-badge :appointment="$appointment" />',
        ['appointment' => new Appointment(['status' => Appointment::STATUS_COMPLETED])]
    );

    $pendingBadge->assertSee('bg-amber-100')->assertSee('text-amber-700');
    $completedBadge->assertSee('bg-emerald-100')->assertSee('text-emerald-700');
});
