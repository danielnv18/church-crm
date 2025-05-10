<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpiritualInformation>
 */
final class SpiritualInformationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'membership_at' => fake()->date(),
            'baptized_at' => fake()->date(),
            'saved_at' => fake()->date(),
            'testimony' => fake()->text(),
        ];
    }
}
