<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vaccination;

class VaccinationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isVeterinaryStaff() || $user->isClient();
    }

    public function view(User $user, Vaccination $vaccination): bool
    {
        return $user->isVeterinaryStaff() || $vaccination->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isVeterinaryStaff();
    }

    public function update(User $user, Vaccination $vaccination): bool
    {
        return $user->isVeterinaryStaff();
    }

}
