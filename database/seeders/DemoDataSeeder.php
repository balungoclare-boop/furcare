<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use App\Services\AppointmentAvailabilityService;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $clientDetails = [
            ['name' => 'Maria Santos', 'email' => 'demo-client@vetclinic.local', 'phone' => '0917-555-0142'],
            ['name' => 'Carlo Reyes', 'email' => 'demo-client-2@vetclinic.local', 'phone' => '0918-555-0276'],
            ['name' => 'Angela Cruz', 'email' => 'demo-client-3@vetclinic.local', 'phone' => '0920-555-0318'],
            ['name' => 'Miguel Garcia', 'email' => 'demo-client-4@vetclinic.local', 'phone' => '0927-555-0461'],
            ['name' => 'Liza Mendoza', 'email' => 'demo-client-5@vetclinic.local', 'phone' => '0995-555-0523'],
        ];
        $clients = collect($clientDetails)->map(fn (array $client): User => User::query()->updateOrCreate(
            ['email' => $client['email']],
            [
                'name' => $client['name'],
                'password' => 'Client@123',
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
                'must_change_password' => false,
            ],
        ));

        $legacyPets = Pet::query()
            ->where('user_id', $clients->first()->id)
            ->whereIn('pet_name', array_map(fn (int $number): string => 'Pet '.$number, range(1, 15)))
            ->get()
            ->keyBy('pet_name');

        $pets = [
            ['owner' => 0, 'name' => 'Biscuit', 'species' => 'Dog', 'breed' => 'Shih Tzu', 'sex' => 'male', 'age' => 4, 'age_type' => 'year', 'type' => Appointment::TYPE_VACCINATION, 'purpose' => 'Anti-Rabies', 'vaccine' => 'Anti-Rabies', 'due_in' => 18, 'day' => 3],
            ['owner' => 0, 'name' => 'Mochi', 'species' => 'Cat', 'breed' => 'Domestic Shorthair', 'sex' => 'female', 'age' => 2, 'age_type' => 'year', 'type' => Appointment::TYPE_CHECKUP, 'purpose' => 'Annual wellness examination', 'vaccine' => 'FVRCP', 'due_in' => 126, 'day' => 3],
            ['owner' => 0, 'name' => 'Coco', 'species' => 'Dog', 'breed' => 'Labrador Retriever', 'sex' => 'female', 'age' => 8, 'age_type' => 'month', 'type' => Appointment::TYPE_GROOMING, 'purpose' => 'Routine grooming', 'grooming' => Appointment::GROOMING_SERVICE_FULL, 'vaccine' => '5-in-1 Vaccine', 'due_in' => 214, 'day' => 3],
            ['owner' => 1, 'name' => 'Pochi', 'species' => 'Dog', 'breed' => 'Pomeranian', 'sex' => 'male', 'age' => 6, 'age_type' => 'year', 'type' => Appointment::TYPE_CHECKUP, 'purpose' => 'Dental and wellness check', 'vaccine' => 'Anti-Rabies', 'due_in' => 82, 'day' => 3],
            ['owner' => 1, 'name' => 'Luna', 'species' => 'Cat', 'breed' => 'Persian', 'sex' => 'female', 'age' => 3, 'age_type' => 'year', 'type' => Appointment::TYPE_VACCINATION, 'purpose' => '5-in-1 Vaccine', 'vaccine' => '5-in-1 Vaccine', 'due_in' => 150, 'day' => 3],
            ['owner' => 1, 'name' => 'Rocky', 'species' => 'Dog', 'breed' => 'Beagle', 'sex' => 'male', 'age' => 5, 'age_type' => 'year', 'type' => Appointment::TYPE_GROOMING, 'purpose' => 'Routine grooming', 'grooming' => Appointment::GROOMING_SERVICE_BASIC, 'vaccine' => 'Anti-Rabies', 'due_in' => 238, 'day' => 3],
            ['owner' => 2, 'name' => 'Pepper', 'species' => 'Cat', 'breed' => 'Domestic Shorthair', 'sex' => 'female', 'age' => 11, 'age_type' => 'month', 'type' => Appointment::TYPE_VACCINATION, 'purpose' => 'Anti-Rabies', 'vaccine' => 'Anti-Rabies', 'due_in' => 35, 'day' => 3],
            ['owner' => 2, 'name' => 'Bruno', 'species' => 'Dog', 'breed' => 'Aspin', 'sex' => 'male', 'age' => 7, 'age_type' => 'year', 'type' => Appointment::TYPE_CHECKUP, 'purpose' => 'Follow-up wellness examination', 'vaccine' => 'Deworming', 'due_in' => 105, 'day' => 10],
            ['owner' => 2, 'name' => 'Daisy', 'species' => 'Dog', 'breed' => 'Golden Retriever', 'sex' => 'female', 'age' => 2, 'age_type' => 'year', 'type' => Appointment::TYPE_VACCINATION, 'purpose' => '6-in-1 Vaccine', 'vaccine' => '6-in-1 Vaccine', 'due_in' => 192, 'day' => 10],
            ['owner' => 3, 'name' => 'Simba', 'species' => 'Cat', 'breed' => 'Bengal', 'sex' => 'male', 'age' => 4, 'age_type' => 'year', 'type' => Appointment::TYPE_CHECKUP, 'purpose' => 'Annual wellness examination', 'vaccine' => 'FVRCP', 'due_in' => 262, 'day' => 10],
            ['owner' => 3, 'name' => 'Nala', 'species' => 'Dog', 'breed' => 'Shih Tzu', 'sex' => 'female', 'age' => 1, 'age_type' => 'year', 'type' => Appointment::TYPE_VACCINATION, 'purpose' => 'Anti-Rabies', 'vaccine' => 'Anti-Rabies', 'due_in' => 14, 'day' => 10],
            ['owner' => 3, 'name' => 'Oreo', 'species' => 'Cat', 'breed' => 'Domestic Shorthair', 'sex' => 'male', 'age' => 5, 'age_type' => 'year', 'type' => Appointment::TYPE_GROOMING, 'purpose' => 'Routine grooming', 'grooming' => Appointment::GROOMING_SERVICE_BASIC, 'vaccine' => 'FVRCP', 'due_in' => 174, 'day' => 10],
            ['owner' => 4, 'name' => 'Max', 'species' => 'Dog', 'breed' => 'German Shepherd', 'sex' => 'male', 'age' => 6, 'age_type' => 'year', 'type' => Appointment::TYPE_CHECKUP, 'purpose' => 'Senior wellness examination', 'vaccine' => 'Anti-Rabies', 'due_in' => 96, 'day' => 17],
            ['owner' => 4, 'name' => 'Ginger', 'species' => 'Cat', 'breed' => 'Persian', 'sex' => 'female', 'age' => 9, 'age_type' => 'month', 'type' => Appointment::TYPE_VACCINATION, 'purpose' => '5-in-1 Vaccine', 'vaccine' => '5-in-1 Vaccine', 'due_in' => 228, 'day' => 17],
            ['owner' => 4, 'name' => 'Buddy', 'species' => 'Dog', 'breed' => 'Aspin', 'sex' => 'male', 'age' => 3, 'age_type' => 'year', 'type' => Appointment::TYPE_CHECKUP, 'purpose' => 'Skin irritation consultation', 'vaccine' => 'Deworming', 'due_in' => 132, 'day' => 17],
        ];

        $appointmentsPerDay = collect($pets)
            ->countBy(fn (array $pet): int => (int) $pet['day']);

        if ($appointmentsPerDay->max() > AppointmentAvailabilityService::DAILY_LIMIT) {
            throw new RuntimeException('Demo appointment schedule exceeds the daily booking limit.');
        }

        foreach ($pets as $index => $details) {
            $owner = $clients[$details['owner']];
            $pet = Pet::query()
                ->where('user_id', $owner->id)
                ->where('pet_name', $details['name'])
                ->first();

            if (! $pet && $legacyPet = $legacyPets->get('Pet '.($index + 1))) {
                $pet = $legacyPet;
            }

            $pet ??= new Pet;
            $pet->fill([
                'user_id' => $owner->id,
                'owner_name' => $owner->name,
                'contact_number' => $clientDetails[$details['owner']]['phone'],
                'pet_name' => $details['name'],
                'species' => $details['species'],
                'breed' => $details['breed'],
                'sex' => $details['sex'],
                'age' => $details['age_type'] === 'year' ? $details['age'] : null,
                'age_value' => $details['age'],
                'age_type' => $details['age_type'],
            ]);
            $pet->save();

            $legacyAppointments = Appointment::query()
                ->where('pet_id', $pet->id)
                ->whereIn('purpose', ['Demo availability sample', 'General Checkup'])
                ->orderBy('id')
                ->get();
            $appointment = $legacyAppointments->first();
            $legacyAppointments->skip(1)->each(fn (Appointment $duplicate) => $duplicate->delete());

            if (! $appointment) {
                $appointment = Appointment::query()
                    ->where('pet_id', $pet->id)
                    ->where('user_id', $owner->id)
                    ->first() ?? new Appointment;
            }

            $appointment->fill([
                'user_id' => $owner->id,
                'pet_id' => $pet->id,
                'type' => $details['type'],
                'appointment_date' => now()->addDays($details['day'])->toDateString(),
                'preferred_time' => ['09:00:00', '10:30:00', '13:00:00', '14:30:00'][$index % 4],
                'purpose' => $details['purpose'],
                'grooming_service_type' => $details['grooming'] ?? null,
                'notes' => match ($details['type']) {
                    Appointment::TYPE_VACCINATION => 'Please bring the pet vaccination record.',
                    Appointment::TYPE_GROOMING => 'Please advise the clinic of any skin sensitivities.',
                    default => 'Please bring any recent medical records.',
                },
                'status' => Appointment::STATUS_PENDING,
                'accepted_at' => $index % 4 === 0 ? now()->subDays(1) : null,
                'is_emergency' => false,
            ]);
            $appointment->save();

            $vaccination = Vaccination::query()
                ->where('pet_id', $pet->id)
                ->first() ?? new Vaccination;
            $vaccination->fill([
                'user_id' => $owner->id,
                'pet_id' => $pet->id,
                'pet_name' => $pet->pet_name,
                'owner_name' => $owner->name,
                'contact_number' => $pet->contact_number,
                'vaccine_name' => $details['vaccine'],
                'date_given' => now()->subDays(180)->toDateString(),
                'next_due_date' => now()->addDays($details['due_in'])->toDateString(),
            ]);
            $vaccination->save();
        }
    }
}
