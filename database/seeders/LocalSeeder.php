<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Person;
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
        $this->seedPeople();
    }

    private function seedUser(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@church.com',
                'roles' => [RoleEnum::Admin],
            ],
            [
                'name' => 'Pastor User',
                'email' => 'pastor@church.com',
                'roles' => [RoleEnum::Pastor],
            ],
            [
                // There is no such thing as a "super pastor" in the bible,
                // but this is just for testing users with both roles
                'name' => 'Super Pastor',
                'email' => 'super@church.com',
                'roles' => [RoleEnum::Admin, RoleEnum::Pastor],
            ],
        ];

        foreach ($users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ])->assignRole($user['roles']);
        }
    }

    private function seedPeople(): void
    {
        // Seed 15 persons
        Person::factory(15)->create();
    }
}
