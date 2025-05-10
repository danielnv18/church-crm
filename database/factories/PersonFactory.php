<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Models\ContactInformation;
use App\Models\SpiritualInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
final class PersonFactory extends Factory
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
            'gender' => fake()->randomElement(array_column(Gender::cases(), 'value')),
            'civil_status' => fake()->randomElement(array_column(CivilStatus::cases(), 'value')),
            'dob' => fake()->date(),
            'spiritual_information' => SpiritualInformation::factory()->create(),
            'contact_information' => ContactInformation::factory()->create(),
        ];
    }
}
