<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

final class CreateUserAction
{
    /**
     * Execute the action.
     */
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            // Create the person with the general information
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole($data['role_ids']);

            $user->refresh();

            return $user;
        });
    }
}
