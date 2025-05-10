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
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dob' => fake()->date(),
        ];
    }
}
