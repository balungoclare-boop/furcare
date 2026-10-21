<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $adminExists = User::query()
            ->whereIn('email', ['admin@vetclinic.local', 'superadmin@vetclinic.local'])
            ->exists();

        if (! $adminExists) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@vetclinic.local',
                'password' => Hash::make('Admin@123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'must_change_password' => true,
            ]);
        }

        $staffExists = User::query()
            ->whereIn('email', ['staff@vetclinic.local', 'vetstaff@vetclinic.local'])
            ->exists();

        if (! $staffExists) {
            User::create([
                'name' => 'Veterinary Staff',
                'email' => 'staff@vetclinic.local',
                'password' => Hash::make('Staff@123'),
                'role' => User::ROLE_VETERINARY_STAFF,
                'is_active' => true,
                'must_change_password' => true,
            ]);
        }
    }
}
