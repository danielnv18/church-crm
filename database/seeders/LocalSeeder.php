<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

final class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedUser();
    }

    private function seedUser(): void
    {
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@church.com', 'role' => RoleEnum::Admin],
            ['name' => 'Pastor User', 'email' => 'pastor@church.com', 'role' => RoleEnum::Pastor],
        ];

        foreach ($users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ])->assignRole($user['role']);
        }
    }
}
