<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ActionEnum;
use App\Models\User;

final class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(ActionEnum::ViewAny->value.' user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Only the user can view their own profile
        if ($user->is($model)) {
            return true;
        }

        // Check if the user has the 'view any user' permission
        return $user->can(ActionEnum::ViewAny->value.' user');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(ActionEnum::Create->value.' user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Only the user can update their own profile
        if ($user->is($model)) {
            return true;
        }

        // Check if the user has the 'update user' permission
        return $user->can(ActionEnum::Update->value.' user');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only the user can delete their own profile
        if ($user->is($model)) {
            return true;
        }

        // Check if the user has the 'delete user' permission
        return $user->can(ActionEnum::Delete->value.' user');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can(ActionEnum::Restore->value.' user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can(ActionEnum::ForceDelete->value.' user');
    }
}
