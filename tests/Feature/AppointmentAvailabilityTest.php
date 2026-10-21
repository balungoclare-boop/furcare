<?php

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;

function createAppointmentForDate(string $date, ?int $userId, int $petId, string $status = Appointment::STATUS_PENDING): Appointment
{
    return Appointment::query()->create([
        'user_id' => $userId,
        'pet_id' => $petId,
        'type' => Appointment::TYPE_CHECKUP,
        'appointment_date' => $date,
        'purpose' => 'Routine checkup',
        'status' => $status,
    ]);
}

test('client appointment form displays the monthly availability calendar', function () {
    $client = User::factory()->create();
    Pet::factory()->create(['user_id' => $client->id]);

    $this->actingAs($client)
        ->get(route('appointments.create'))
        ->assertOk()
        ->assertSee('appointment-calendar')
        ->assertSee('Up to 15 appointments are available each day.');
});

test('client rescheduling hides the status field but retains its submitted value', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create(['user_id' => $client->id]);
    $appointment = createAppointmentForDate(today()->addDays(2)->toDateString(), $client->id, $pet->id);

    $this->actingAs($client)
        ->get(route('appointments.edit', $appointment))
        ->assertOk()
        ->assertDontSee('for="status"', false)
        ->assertDontSee('id="status"', false)
        ->assertSee('name="status" value="pending"', false);
});

test('clients can see remaining appointment capacity for each date', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create(['user_id' => $client->id]);
    $date = today()->addDays(5)->toDateString();
    $nextDate = today()->addDays(6)->toDateString();

    createAppointmentForDate($date, null, $pet->id);
    createAppointmentForDate($date, null, $pet->id);
    createAppointmentForDate($date, null, $pet->id, Appointment::STATUS_CANCELLED);
    createAppointmentForDate($nextDate, null, $pet->id);

    $this->actingAs($client)
        ->getJson(route('appointments.availability', ['month' => today()->addDays(5)->format('Y-m')]))
        ->assertOk()
        ->assertJsonPath('limit', 15)
        ->assertJsonPath("booked.{$date}", 2)
        ->assertJsonPath("booked.{$nextDate}", 1);
});

test('clients cannot book more than fifteen appointments on the same day', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create(['user_id' => $client->id]);
    $date = today()->addDays(5)->toDateString();

    foreach (range(1, 15) as $booking) {
        createAppointmentForDate($date, null, $pet->id);
    }

    $this->actingAs($client)
        ->from(route('appointments.create'))
        ->post(route('appointments.store'), [
            'pet_id' => $pet->id,
            'type' => Appointment::TYPE_CHECKUP,
            'appointment_date' => $date,
            'purpose' => 'Routine checkup',
            'checkup_purpose' => 'Routine checkup',
            'status' => Appointment::STATUS_PENDING,
        ])
        ->assertSessionHasErrors('appointment_date');

    expect(Appointment::query()->whereDate('appointment_date', $date)->count())->toBe(15);
});

test('clients can book the last available appointment of the day', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create(['user_id' => $client->id]);
    $date = today()->addDays(5)->toDateString();

    foreach (range(1, 14) as $booking) {
        createAppointmentForDate($date, null, $pet->id);
    }

    $this->actingAs($client)
        ->post(route('appointments.store'), [
            'pet_id' => $pet->id,
            'type' => Appointment::TYPE_CHECKUP,
            'appointment_date' => $date,
            'purpose' => 'Routine checkup',
            'checkup_purpose' => 'Routine checkup',
            'status' => Appointment::STATUS_PENDING,
        ])
        ->assertRedirect(route('appointments.index'))
        ->assertSessionHas('success', 'Appointment created successfully.');

    expect(Appointment::query()->whereDate('appointment_date', $date)->count())->toBe(15);
});

test('clients cannot reschedule into a fully booked day', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create(['user_id' => $client->id]);
    $appointmentDate = today()->addDays(4)->toDateString();
    $appointment = createAppointmentForDate($appointmentDate, $client->id, $pet->id);
    $fullDate = today()->addDays(5)->toDateString();

    foreach (range(1, 15) as $booking) {
        createAppointmentForDate($fullDate, null, $pet->id);
    }

    $this->actingAs($client)
        ->from(route('appointments.edit', $appointment))
        ->put(route('appointments.update', $appointment), [
            'pet_id' => $pet->id,
            'type' => Appointment::TYPE_CHECKUP,
            'appointment_date' => $fullDate,
            'purpose' => 'Routine checkup',
            'checkup_purpose' => 'Routine checkup',
            'status' => Appointment::STATUS_PENDING,
        ])
        ->assertSessionHasErrors('appointment_date');

    expect($appointment->refresh()->appointment_date->toDateString())->toBe($appointmentDate);
});

test('clients cannot create appointments for past dates', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create(['user_id' => $client->id]);

    $this->actingAs($client)
        ->from(route('appointments.create'))
        ->post(route('appointments.store'), [
            'pet_id' => $pet->id,
            'type' => Appointment::TYPE_CHECKUP,
            'appointment_date' => today()->subDay()->toDateString(),
            'purpose' => 'Routine checkup',
            'checkup_purpose' => 'Routine checkup',
            'status' => Appointment::STATUS_PENDING,
        ])
        ->assertSessionHasErrors('appointment_date');

    expect(Appointment::query()->count())->toBe(0);
});

test('duplicate staff walk-in submissions do not create orphan pets', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $payload = [
        'pet_name' => 'Buddy',
        'owner_name' => 'Taylor Owner',
        'contact_number' => '555-0100',
        'type' => Appointment::TYPE_CHECKUP,
        'appointment_date' => today()->addDays(5)->toDateString(),
        'purpose' => 'Routine checkup',
        'checkup_purpose' => 'Routine checkup',
        'status' => Appointment::STATUS_PENDING,
    ];

    $this->actingAs($staff)
        ->post(route('appointments.store'), $payload)
        ->assertRedirect(route('appointments.index'));

    $this->actingAs($staff)
        ->from(route('appointments.create'))
        ->post(route('appointments.store'), $payload)
        ->assertSessionHas('error', 'Duplicate submission detected. Please wait a few seconds before submitting again.');

    expect(Pet::query()->count())->toBe(1)
        ->and(Appointment::query()->count())->toBe(1);
});
