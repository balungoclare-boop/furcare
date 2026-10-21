<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

class PetPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isVeterinaryStaff() || $user->isClient();
    }

    public function view(User $user, Pet $pet): bool
    {
        return $user->isVeterinaryStaff() || $pet->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isVeterinaryStaff() || $user->isClient();
    }

    public function update(User $user, Pet $pet): bool
    {
        return $user->isVeterinaryStaff() || $pet->user_id === $user->id;
    }

}
