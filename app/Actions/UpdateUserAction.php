<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateUserAction
{
    /**
     * Execute the action.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {

            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // Update the password only if it is provided
            if (isset($data['password'])) {
                $user->update([
                    'password' => bcrypt($data['password']),
                ]);
            }

            $user->syncRoles($data['role_ids']);

            return $user;
        });
    }
}
