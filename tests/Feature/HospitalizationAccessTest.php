<?php

use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\User;

test('veterinary staff can view and update hospitalizations created by other staff', function () {
    $creator = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $viewer = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $pet = Pet::factory()->create([
        'pet_name' => 'Max',
        'owner_name' => 'Taylor',
    ]);
    $hospitalization = Hospitalization::query()->create([
        'pet_id' => $pet->id,
        'user_id' => $creator->id,
        'admitted_date' => today()->subDay(),
        'status' => Hospitalization::STATUS_ACTIVE,
        'notes' => 'Initial observation',
    ]);

    $this->actingAs($viewer)
        ->get(route('hospitalizations.index'))
        ->assertOk()
        ->assertSee('Max');

    $this->get(route('hospitalizations.show', $hospitalization))->assertOk();
    $this->get(route('hospitalizations.edit', $hospitalization))->assertOk();

    $this->patch(route('hospitalizations.update', $hospitalization), [
        'pet_name' => 'Max',
        'owner_name' => 'Taylor',
        'admitted_date' => today()->subDay()->toDateString(),
        'discharge_date' => null,
        'status' => Hospitalization::STATUS_ACTIVE,
        'notes' => 'Updated by another veterinarian',
        'medication_schedule' => null,
        'discharge_summary' => null,
    ])->assertRedirect(route('hospitalizations.index'));

    expect($hospitalization->fresh()->notes)->toBe('Updated by another veterinarian')
        ->and($hospitalization->fresh()->user_id)->toBe($creator->id);
});

test('clients cannot access hospitalization records', function () {
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

    $this->actingAs($client)
        ->get(route('hospitalizations.index'))
        ->assertForbidden();
});
