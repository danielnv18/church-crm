<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateUserAction
{
    /**
     * Execute the action.
     */
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            // Create the person with the general information
            $generalData = [
                'name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'gender' => $data['gender'],
                'civil_status' => $data['civil_status'] ?? null,
                'dob' => $data['dob'] ?? null,
            ];
            $user = User::create($generalData);

        });
    }
}
