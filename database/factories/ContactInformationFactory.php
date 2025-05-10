<?php

declare(strict_types=1);

namespace Database\Factories;

namespace Database\Factories;

use App\Models\ContactInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactInformation>
 */
final class ContactInformationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'alternate_phone' => fake()->phoneNumber(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
        ];
    }
}
