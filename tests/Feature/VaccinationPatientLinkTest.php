<?php

use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use App\Services\DashboardService;

test('recorded vaccinations can be linked to a pet and appear in owner and patient history', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $owner = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $owner->id,
        'pet_name' => 'Biscuit',
        'owner_name' => $owner->name,
        'contact_number' => '0917-555-0100',
    ]);

    $this->actingAs($staff)
        ->get(route('vaccinations.create'))
        ->assertOk()
        ->assertSee('Link to Registered Pet (optional)')
        ->assertSee('Biscuit');

    $this->actingAs($staff)
        ->post(route('vaccinations.store'), [
            'pet_id' => $pet->id,
            'pet_name' => 'Untrusted submitted name',
            'owner_name' => 'Untrusted submitted owner',
            'contact_number' => '000',
            'vaccine_name' => 'Anti-Rabies',
            'date_given' => today()->subDay()->toDateString(),
        ])
        ->assertRedirect();

    $vaccination = Vaccination::query()->sole();

    expect($vaccination->pet_id)->toBe($pet->id)
        ->and($vaccination->user_id)->toBe($owner->id)
        ->and($vaccination->pet_name)->toBe('Biscuit')
        ->and($vaccination->owner_name)->toBe($owner->name)
        ->and($vaccination->contact_number)->toBe('0917-555-0100')
        ->and($pet->fresh()->vaccinations()->count())->toBe(1)
        ->and(app(DashboardService::class)->clientOverview($owner)['metrics']['upcomingVaccinations'])->toBe(1);

    $this->get(route('vaccinations.edit', $vaccination))
        ->assertOk()
        ->assertSee('value="'.$pet->id.'"', false);
});

test('vaccination records can still be entered without a registered patient', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);

    $this->actingAs($staff)
        ->post(route('vaccinations.store'), [
            'pet_name' => 'Walk-in Pet',
            'owner_name' => 'Walk-in Owner',
            'contact_number' => '0917-555-0200',
            'vaccine_name' => 'Anti-Rabies',
            'date_given' => today()->subDay()->toDateString(),
        ])
        ->assertRedirect();

    $vaccination = Vaccination::query()->sole();

    expect($vaccination->pet_id)->toBeNull()
        ->and($vaccination->user_id)->toBe($staff->id)
        ->and($vaccination->pet_name)->toBe('Walk-in Pet');
});
