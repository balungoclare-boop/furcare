<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentAvailabilityService
{
    public const DAILY_LIMIT = 15;

    public function bookedAppointmentsBetween(
        CarbonImmutable $start,
        CarbonImmutable $end,
        ?int $excludeAppointmentId = null
    ): array {
        $query = Appointment::query()
            ->whereDate('appointment_date', '>=', $start->toDateString())
            ->whereDate('appointment_date', '<=', $end->toDateString())
            ->whereNotIn('status', [
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_REJECTED,
            ]);

        if ($excludeAppointmentId !== null) {
            $query->whereKeyNot($excludeAppointmentId);
        }

        return $query
            ->selectRaw('DATE(appointment_date) as appointment_day, COUNT(*) as booked')
            ->groupBy('appointment_day')
            ->pluck('booked', 'appointment_day')
            ->map(fn ($booked): int => (int) $booked)
            ->all();
    }

    public function lockAndEnsureAvailable(string $date, ?int $excludeAppointmentId = null): void
    {
        DB::table('appointment_capacity_locks')
            ->where('id', 1)
            ->lockForUpdate()
            ->first();

        $query = Appointment::query()
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', [
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_REJECTED,
            ]);

        if ($excludeAppointmentId !== null) {
            $query->whereKeyNot($excludeAppointmentId);
        }

        if ($query->count() >= self::DAILY_LIMIT) {
            throw ValidationException::withMessages([
                'appointment_date' => 'This date is fully booked. Please choose another date.',
            ]);
        }
    }
}
