<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isVeterinaryStaff() || $user->isClient();
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isVeterinaryStaff() || $appointment->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isVeterinaryStaff() || $user->isClient();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isVeterinaryStaff() || $appointment->user_id === $user->id;
    }

    public function approve(User $user, Appointment $appointment): bool
    {
        return $user->isVeterinaryStaff();
    }

    public function complete(User $user, Appointment $appointment): bool
    {
        return $user->isVeterinaryStaff();
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        if ($user->isVeterinaryStaff()) {
            return true;
        }

        return $user->isClient()
            && $appointment->user_id === $user->id
            && $appointment->status === Appointment::STATUS_PENDING;
    }
}
