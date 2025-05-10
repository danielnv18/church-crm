<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ActionEnum;
use App\Models\Person;
use App\Models\User;

final class PersonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(ActionEnum::ViewAny->value.' person');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Person $person): bool
    {
        return $user->can(ActionEnum::View->value.' person');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(ActionEnum::Create->value.' person');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Person $person): bool
    {
        return $user->can(ActionEnum::Update->value.' person');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Person $person): bool
    {
        return $user->can(ActionEnum::Delete->value.' person');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Person $person): bool
    {
        return $user->can(ActionEnum::Restore->value.' person');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Person $person): bool
    {
        return $user->can(ActionEnum::ForceDelete->value.' person');
    }
}
