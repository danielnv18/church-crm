<?php

declare(strict_types=1);

use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can create a person with valid attributes', function (): void {
    // Arrange
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => Gender::Male,
        'civil_status' => CivilStatus::Single,
        'dob' => '1990-01-01',
    ];

    // Act
    $person = Person::create($data);

    // Assert
    expect($person)->toBeInstanceOf(Person::class)
        ->and($person->first_name)->toBe('John')
        ->and($person->last_name)->toBe('Doe')
        ->and($person->gender)->toBe(Gender::Male)
        ->and($person->civil_status)->toBe(CivilStatus::Single)
        ->and($person->dob->format('Y-m-d'))->toBe('1990-01-01');

    $this->assertDatabaseHas('people', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => 'male',
        'civil_status' => 'single',
    ]);
});

it('casts gender and civil_status to enums', function (): void {
    // Arrange & Act
    $person = Person::create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'gender' => 'female',
        'civil_status' => 'married',
        'dob' => '1985-05-15',
    ]);

    // Assert
    expect($person->gender)->toBe(Gender::Female)
        ->and($person->gender)->toBeInstanceOf(Gender::class)
        ->and($person->civil_status)->toBe(CivilStatus::Married)
        ->and($person->civil_status)->toBeInstanceOf(CivilStatus::class);
});
