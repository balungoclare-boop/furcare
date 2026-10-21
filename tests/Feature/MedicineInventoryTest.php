<?php

use App\Models\AuditLog;
use App\Models\Medicine;
use App\Models\User;

test('medicine stock changes are recorded with an auditable quantity delta', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $medicine = Medicine::query()->create([
        'name' => 'Dewormer',
        'stock_quantity' => 20,
        'low_stock_threshold' => 5,
    ]);

    $this->actingAs($staff)
        ->put(route('medicines.update', $medicine), [
            'name' => 'Dewormer',
            'stock_quantity' => 14,
            'stock_adjustment_reason' => 'Six units issued for clinic use.',
            'low_stock_threshold' => 5,
        ])
        ->assertRedirect(route('medicines.index'));

    $auditLog = AuditLog::query()
        ->where('entity_type', 'medicine')
        ->where('entity_id', $medicine->id)
        ->sole();

    expect($medicine->fresh()->stock_quantity)->toBe(14)
        ->and($auditLog->user_id)->toBe($staff->id)
        ->and($auditLog->event_type)->toBe('stock_adjusted')
        ->and($auditLog->context)->toBe([
            'previous_stock_quantity' => 20,
            'new_stock_quantity' => 14,
            'quantity_change' => -6,
            'reason' => 'Six units issued for clinic use.',
        ]);
});

test('medicine stock cannot be changed without an adjustment reason', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $medicine = Medicine::query()->create([
        'name' => 'Dewormer',
        'stock_quantity' => 20,
        'low_stock_threshold' => 5,
    ]);

    $this->actingAs($staff)
        ->put(route('medicines.update', $medicine), [
            'name' => 'Dewormer',
            'stock_quantity' => 14,
            'low_stock_threshold' => 5,
        ])
        ->assertSessionHasErrors('stock_adjustment_reason');

    expect($medicine->fresh()->stock_quantity)->toBe(20)
        ->and(AuditLog::query()->count())->toBe(0);
});

test('opening stock above zero requires a reason', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);

    $this->actingAs($staff)
        ->post(route('medicines.store'), [
            'name' => 'Dewormer',
            'stock_quantity' => 20,
            'low_stock_threshold' => 5,
        ])
        ->assertSessionHasErrors('stock_adjustment_reason');

    expect(Medicine::query()->count())->toBe(0);
});

test('non-stock medicine edits do not create a stock adjustment event', function () {
    $staff = User::factory()->create(['role' => User::ROLE_VETERINARY_STAFF]);
    $medicine = Medicine::query()->create([
        'name' => 'Dewormer',
        'stock_quantity' => 20,
        'low_stock_threshold' => 5,
    ]);

    $this->actingAs($staff)
        ->put(route('medicines.update', $medicine), [
            'name' => 'Dewormer',
            'stock_quantity' => 20,
            'supplier' => 'New supplier',
            'low_stock_threshold' => 5,
        ])
        ->assertRedirect(route('medicines.index'));

    expect(AuditLog::query()->where('entity_type', 'medicine')->sole()->event_type)
        ->toBe('updated');
});
