<?php

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Notifications\AppointmentAcceptedNotification;
use App\Notifications\AppointmentStatusNotification;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

function makeNotifiableAppointment(User $client, string $status = Appointment::STATUS_PENDING): Appointment
{
    $pet = Pet::factory()->create(['user_id' => $client->id]);

    return Appointment::query()->create([
        'user_id' => $client->id,
        'pet_id' => $pet->id,
        'type' => Appointment::TYPE_CHECKUP,
        'appointment_date' => today()->addDays(3)->toDateString(),
        'purpose' => 'Routine checkup',
        'status' => $status,
    ]);
}

test('approval notifications are available in the database as well as by email', function () {
    $client = User::factory()->create();
    $appointment = makeNotifiableAppointment($client);

    expect((new AppointmentAcceptedNotification($appointment))->via($client))
        ->toContain('database')
        ->toContain('mail');
});

test('staff appointment actions do not include a reject action', function () {
    $client = User::factory()->create();
    $appointment = makeNotifiableAppointment($client);

    $this->blade('<x-ui.appointment-status-menu :appointment="$appointment" />', [
        'appointment' => $appointment,
    ])
        ->assertSee('Accept')
        ->assertDontSee('Reject');
});

test('staff cancellation notifies the client', function () {
    Notification::fake();
    $client = User::factory()->create();
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $appointment = makeNotifiableAppointment($client);

    $this->actingAs($staff)
        ->patch(route('appointments.cancel', $appointment))
        ->assertRedirect()
        ->assertSessionHas('success', 'Appointment cancelled successfully.');

    expect($appointment->fresh()->status)->toBe(Appointment::STATUS_CANCELLED);
    Notification::assertSentTo($client, AppointmentStatusNotification::class);
});

test('clients cannot approve or complete their own appointments', function () {
    $client = User::factory()->create();
    $pending = makeNotifiableAppointment($client);
    $accepted = makeNotifiableAppointment($client);
    $accepted->update(['accepted_at' => now()]);

    $this->actingAs($client)
        ->patch(route('appointments.approve', $pending))
        ->assertForbidden();

    $this->actingAs($client)
        ->patch(route('appointments.complete', $accepted))
        ->assertForbidden();

    expect($pending->fresh()->accepted_at)->toBeNull()
        ->and($accepted->fresh()->status)->toBe(Appointment::STATUS_PENDING);
});

test('staff can approve and complete appointments', function () {
    Notification::fake();
    $client = User::factory()->create();
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $pending = makeNotifiableAppointment($client);
    $accepted = makeNotifiableAppointment($client);
    $accepted->update(['accepted_at' => now()]);

    $this->actingAs($staff)
        ->patch(route('appointments.approve', $pending))
        ->assertRedirect()
        ->assertSessionHas('success', 'Appointment approved successfully.');

    $this->actingAs($staff)
        ->patch(route('appointments.complete', $accepted))
        ->assertRedirect()
        ->assertSessionHas('success', 'Appointment marked as completed.');

    expect($pending->fresh()->accepted_at)->not->toBeNull()
        ->and($pending->fresh()->staff_id)->toBe($staff->id)
        ->and($accepted->fresh()->status)->toBe(Appointment::STATUS_COMPLETED);

    Notification::assertSentTo($client, \App\Notifications\AppointmentAcceptedNotification::class);
});

test('an accepted appointment cannot be approved again by another staff member', function () {
    Notification::fake();
    $client = User::factory()->create();
    $firstStaff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $otherStaff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $appointment = makeNotifiableAppointment($client);

    $this->actingAs($firstStaff)
        ->patch(route('appointments.approve', $appointment))
        ->assertSessionHas('success', 'Appointment approved successfully.');

    $acceptedAt = $appointment->fresh()->accepted_at;
    $this->actingAs($otherStaff)
        ->patch(route('appointments.approve', $appointment))
        ->assertSessionHas('error', 'Only unaccepted pending appointments can be approved.');

    expect($appointment->fresh()->accepted_at->equalTo($acceptedAt))->toBeTrue()
        ->and($appointment->fresh()->staff_id)->toBe($firstStaff->id);
    Notification::assertSentToTimes($client, \App\Notifications\AppointmentAcceptedNotification::class, 1);
});

test('admin dashboard reports every active operational staff member', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $otherStaff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $inactiveStaff = User::factory()->create([
        'role' => User::ROLE_VETERINARY_STAFF,
        'is_active' => false,
    ]);
    $client = User::factory()->create();
    $appointment = makeNotifiableAppointment($client);

    $this->actingAs($staff)
        ->patch(route('appointments.approve', $appointment))
        ->assertRedirect();

    $performance = app(DashboardService::class)->adminOverview()['staffPerformance'];

    expect($performance)->toHaveCount(3)
        ->and($performance->pluck('staff_id')->all())
        ->toEqualCanonicalizing([$admin->id, $staff->id, $otherStaff->id])
        ->and($performance->firstWhere('staff_id', $staff->id)['total_consultations'])
        ->toBe(1)
        ->and($performance->pluck('staff_id'))->not->toContain($inactiveStaff->id);
});

test('clients see their own pending appointment count and can mark notifications read', function () {
    $client = User::factory()->create();
    makeNotifiableAppointment($client);
    $notification = $client->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => AppointmentStatusNotification::class,
        'data' => ['message' => 'Your appointment was cancelled by the clinic.'],
    ]);

    $this->actingAs($client)
        ->get(route('appointments.index'))
        ->assertOk()
        ->assertSee('1 pending appointments')
        ->assertSee('Your appointment was cancelled by the clinic.')
        ->assertSee('<title>Appointments |', false)
        ->assertDontSee('Medicines');

    $this->actingAs($client)
        ->post(route('notifications.read', $notification->id))
        ->assertRedirect(route('appointments.index'));

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('client dashboard pending count excludes accepted appointments', function () {
    $client = User::factory()->create();
    makeNotifiableAppointment($client);
    $acceptedAppointment = makeNotifiableAppointment($client);
    $acceptedAppointment->update(['accepted_at' => now()]);

    $metrics = app(\App\Services\DashboardService::class)->clientOverview($client)['metrics'];

    expect($metrics['pendingAppointments'])->toBe(1);
});

test('client dashboard upcoming appointments excludes terminal statuses', function () {
    $client = User::factory()->create();
    $pending = makeNotifiableAppointment($client);
    $cancelled = makeNotifiableAppointment($client);
    $cancelled->update(['status' => Appointment::STATUS_CANCELLED]);
    $completed = makeNotifiableAppointment($client);
    $completed->update(['status' => Appointment::STATUS_COMPLETED]);

    $upcoming = app(\App\Services\DashboardService::class)->clientOverview($client)['upcomingAppointments'];

    expect($upcoming->modelKeys())->toBe([$pending->id]);
});

test('staff shell groups clinic, inventory, communications, and admin navigation', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);

    $this->actingAs($staff)
        ->get(route('appointments.index'))
        ->assertOk()
        ->assertSee('Clinic')
        ->assertSee('Inventory')
        ->assertSee('Communication')
        ->assertSee('Admin')
        ->assertSee('Medicines')
        ->assertSee('Announcements')
        ->assertSee('Reports')
        ->assertDontSee('User Management');
});
