<?php

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;

test('pets can be searched by pet or owner name', function () {
    $client = User::factory()->create();
    Pet::factory()->create([
        'user_id' => $client->id,
        'pet_name' => 'Clover',
        'owner_name' => 'Rita Santos',
    ]);
    Pet::factory()->create([
        'user_id' => $client->id,
        'pet_name' => 'Biscuit',
        'owner_name' => 'Alex Cruz',
    ]);

    $this->actingAs($client)
        ->get(route('pets.index', ['search' => 'Rita']))
        ->assertOk()
        ->assertSee('Clover')
        ->assertDontSee('Biscuit');
});

test('appointments can be searched by pet name or purpose', function () {
    $client = User::factory()->create();
    $matchingPet = Pet::factory()->create([
        'user_id' => $client->id,
        'pet_name' => 'Clover',
    ]);
    $otherPet = Pet::factory()->create([
        'user_id' => $client->id,
        'pet_name' => 'Biscuit',
    ]);

    foreach ([
        ['pet_id' => $matchingPet->id, 'purpose' => 'Routine checkup'],
        ['pet_id' => $otherPet->id, 'purpose' => 'Dental consultation'],
    ] as $appointmentDetails) {
        Appointment::query()->create([
            'user_id' => $client->id,
            'pet_id' => $appointmentDetails['pet_id'],
            'type' => Appointment::TYPE_CHECKUP,
            'appointment_date' => '2026-10-20',
            'purpose' => $appointmentDetails['purpose'],
            'status' => Appointment::STATUS_PENDING,
        ]);
    }

    $this->actingAs($client)
        ->get(route('appointments.index', ['search' => 'Clover']))
        ->assertOk()
        ->assertSee('Clover')
        ->assertDontSee('Biscuit');
});

test('vaccinations can be searched by pet or vaccine name', function () {
    $client = User::factory()->create();
    $matchingPet = Pet::factory()->create([
        'user_id' => $client->id,
        'pet_name' => 'Clover',
    ]);
    $otherPet = Pet::factory()->create([
        'user_id' => $client->id,
        'pet_name' => 'Biscuit',
    ]);

    Vaccination::query()->create([
        'user_id' => $client->id,
        'pet_id' => $matchingPet->id,
        'pet_name' => $matchingPet->pet_name,
        'owner_name' => $matchingPet->owner_name,
        'vaccine_name' => 'Anti-Rabies',
        'date_given' => '2026-01-15',
        'next_due_date' => '2027-01-15',
    ]);
    Vaccination::query()->create([
        'user_id' => $client->id,
        'pet_id' => $otherPet->id,
        'pet_name' => $otherPet->pet_name,
        'owner_name' => $otherPet->owner_name,
        'vaccine_name' => 'FVRCP',
        'date_given' => '2026-01-15',
        'next_due_date' => '2027-01-15',
    ]);

    $this->actingAs($client)
        ->get(route('vaccinations.index', ['search' => 'Anti-Rabies']))
        ->assertOk()
        ->assertSee('Clover')
        ->assertDontSee('Biscuit');
});
