<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateUserAction
{
    /**
     * Execute the action.
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user): User {

            $user->syncRoles($data['role_ids']);
            return $user;
        });
    }
}
