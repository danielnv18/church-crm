<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionModelAction;
use App\Models\User;

final class PersonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionModelAction::ViewAny->value.' person');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->can(PermissionModelAction::View->value.' person');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionModelAction::Create->value.' person');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->can(PermissionModelAction::Update->value.' person');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->can(PermissionModelAction::Delete->value.' person');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->can(PermissionModelAction::Restore->value.' person');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(PermissionModelAction::ForceDelete->value.' person');
    }
}
