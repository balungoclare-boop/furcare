<?php

use App\Models\Pet;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('default database seeder does not attach demo records to an existing client', function () {
    $client = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $client->id,
        'owner_name' => $client->name,
    ]);

    $this->seed(DatabaseSeeder::class);

    $demoClientIds = User::query()
        ->where('email', 'like', 'demo-client%@vetclinic.local')
        ->pluck('id');
    $appointmentsPerDate = DB::table('appointments')
        ->whereIn('user_id', $demoClientIds)
        ->selectRaw('DATE(appointment_date) as appointment_day, COUNT(*) as total')
        ->groupBy('appointment_day')
        ->orderByDesc('total')
        ->pluck('total')
        ->all();
    $appointmentTypes = DB::table('appointments')
        ->whereIn('user_id', $demoClientIds)
        ->distinct()
        ->pluck('type')
        ->all();

    expect(Pet::query()->count())->toBe(16)
        ->and($pet->refresh()->user_id)->toBe($client->id)
        ->and($client->fresh()->email)->toBe($client->email)
        ->and($demoClientIds)->toHaveCount(5)
        ->and($appointmentsPerDate)->toBe([7, 5, 3])
        ->and($appointmentTypes)->toContain('vaccination', 'checkup', 'grooming')
        ->and(Pet::query()->whereIn('user_id', $demoClientIds)->where('pet_name', 'like', 'Pet %')->exists())->toBeFalse()
        ->and(DB::table('appointments')->whereIn('user_id', $demoClientIds)->where('purpose', 'Demo availability sample')->exists())->toBeFalse()
        ->and(DB::table('appointments')->whereIn('user_id', $demoClientIds)->whereNotNull('accepted_at')->count())->toBeGreaterThan(0);

    $this->seed(DemoDataSeeder::class);

    expect(Pet::query()->whereIn('user_id', $demoClientIds)->count())->toBe(15)
        ->and(DB::table('appointments')->whereIn('user_id', $demoClientIds)->count())->toBe(15)
        ->and(DB::table('vaccinations')->whereIn('user_id', $demoClientIds)->count())->toBe(15);
});

test('default account seeders do not run outside local or testing environments', function () {
    $originalEnvironment = app()->environment();
    app()->detectEnvironment(fn () => 'production');

    try {
        app(DatabaseSeeder::class)->run();
    } finally {
        app()->detectEnvironment(fn () => $originalEnvironment);
    }

    expect(User::query()->count())->toBe(0);
});

test('admin seeder creates missing defaults without resetting existing accounts', function () {
    $adminPassword = 'UniqueAdminPassword@123';
    $staffPassword = 'UniqueStaffPassword@123';
    $admin = User::factory()->create([
        'name' => 'Clinic Owner',
        'email' => 'admin@vetclinic.local',
        'password' => Hash::make($adminPassword),
        'role' => User::ROLE_ADMIN,
        'is_active' => false,
        'must_change_password' => false,
    ]);
    $staff = User::factory()->create([
        'name' => 'Lead Veterinarian',
        'email' => 'vetstaff@vetclinic.local',
        'password' => Hash::make($staffPassword),
        'role' => User::ROLE_VETERINARY_STAFF,
        'is_active' => false,
        'must_change_password' => false,
    ]);

    $this->seed(AdminSeeder::class);

    expect(User::query()->count())->toBe(2)
        ->and($admin->fresh()->name)->toBe('Clinic Owner')
        ->and($admin->fresh()->is_active)->toBeFalse()
        ->and($admin->fresh()->must_change_password)->toBeFalse()
        ->and(Hash::check($adminPassword, $admin->fresh()->password))->toBeTrue()
        ->and($staff->fresh()->name)->toBe('Lead Veterinarian')
        ->and($staff->fresh()->is_active)->toBeFalse()
        ->and($staff->fresh()->must_change_password)->toBeFalse()
        ->and(Hash::check($staffPassword, $staff->fresh()->password))->toBeTrue();
});
