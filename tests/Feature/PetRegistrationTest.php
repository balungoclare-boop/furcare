<?php

use App\Models\Pet;
use App\Models\User;

test('client sees the patient registration flow without an owner contact field', function () {
    $client = User::factory()->create(['name' => 'Taylor Owner']);

    $response = $this->actingAs($client)->get(route('pets.create'));

    $response->assertOk()
        ->assertSee('Registering for:')
        ->assertSee('Taylor Owner')
        ->assertSee('Basic info')
        ->assertSee('Health notes')
        ->assertSee('Save and book appointment')
        ->assertDontSee('Contact Number');
});

test('client can register a pet with a date of birth and health details', function () {
    $client = User::factory()->create();

    $response = $this->actingAs($client)->post(route('pets.store'), [
        'owner_user_id' => $client->id,
        'pet_name' => 'Max',
        'species' => 'Dog',
        'breed' => 'Golden Retriever',
        'sex' => 'unknown',
        'age_is_approximate' => '0',
        'date_of_birth' => '2022-04-12 00:00:00',
        'weight' => '12.50',
        'color' => 'Golden',
        'is_neutered' => '1',
        'microchip_number' => '985100000000001',
        'allergies' => 'Chicken',
        'notes' => 'Sensitive stomach',
    ]);

    $pet = Pet::query()->where('pet_name', 'Max')->firstOrFail();

    $response->assertRedirect(route('pets.edit', $pet));
    expect($pet->date_of_birth->toDateString())->toBe('2022-04-12');
    $this->assertDatabaseHas('pets', [
        'id' => $pet->id,
        'age_value' => null,
        'weight' => 12.5,
        'color' => 'Golden',
        'is_neutered' => true,
        'microchip_number' => '985100000000001',
        'allergies' => 'Chicken',
        'notes' => 'Sensitive stomach',
    ]);
});

test('client can register a pet using an approximate age', function () {
    $client = User::factory()->create();

    $response = $this->actingAs($client)->post(route('pets.store'), [
        'owner_user_id' => $client->id,
        'pet_name' => 'Milo',
        'species' => 'Cat',
        'sex' => 'male',
        'age_is_approximate' => '1',
        'age_value' => '8',
        'age_type' => 'month',
        'submit_action' => 'save_book_appointment',
    ]);

    $pet = Pet::query()->where('pet_name', 'Milo')->firstOrFail();

    $response->assertRedirect(route('appointments.create', ['pet_id' => $pet->id]));
    $this->assertDatabaseHas('pets', [
        'id' => $pet->id,
        'date_of_birth' => null,
        'age_value' => 8,
        'age_type' => 'month',
        'age' => 0,
    ]);
});

test('pet registration requires a date of birth or an approximate age', function () {
    $client = User::factory()->create();

    $response = $this->actingAs($client)->from(route('pets.create'))->post(route('pets.store'), [
        'owner_user_id' => $client->id,
        'pet_name' => 'Pip',
        'species' => 'Rabbit',
        'sex' => 'female',
        'age_is_approximate' => '0',
        'date_of_birth' => '',
    ]);

    $response->assertRedirect(route('pets.create'))
        ->assertSessionHasErrors('date_of_birth');
});

test('pet edits retain an exact date of birth until it is cleared', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $client->id,
        'date_of_birth' => '2022-04-12',
        'age' => 4,
        'age_value' => null,
        'age_type' => null,
    ]);

    $this->actingAs($client)
        ->put(route('pets.update', $pet), [
            'owner_user_id' => $client->id,
            'pet_name' => $pet->pet_name,
            'species' => $pet->species,
            'breed' => $pet->breed,
            'sex' => $pet->sex,
            'age_value' => 4,
            'age_type' => 'year',
            'date_of_birth' => '2022-04-12',
        ])
        ->assertRedirect(route('pets.index'));

    expect($pet->fresh()->date_of_birth->toDateString())->toBe('2022-04-12');
});

test('pet edits can replace an exact date of birth with an approximate age', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $client->id,
        'date_of_birth' => '2022-04-12',
        'age' => 4,
        'age_value' => null,
        'age_type' => null,
    ]);

    $this->actingAs($client)
        ->put(route('pets.update', $pet), [
            'owner_user_id' => $client->id,
            'pet_name' => $pet->pet_name,
            'species' => $pet->species,
            'breed' => $pet->breed,
            'sex' => $pet->sex,
            'age_value' => 8,
            'age_type' => 'month',
            'date_of_birth' => '',
        ])
        ->assertRedirect(route('pets.index'));

    expect($pet->fresh()->date_of_birth)->toBeNull()
        ->and($pet->fresh()->age_value)->toBe(8)
        ->and($pet->fresh()->display_age)->toBe('8 months');
});
