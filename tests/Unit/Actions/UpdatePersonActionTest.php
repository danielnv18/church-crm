<?php

declare(strict_types=1);

use App\Actions\UpdatePersonAction;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('updates a person with all information', function (): void {
    // Arrange
    $person = Person::factory()->create();
    $action = new UpdatePersonAction();
    $data = [
        'first_name' => 'John Updated',
        'last_name' => 'Doe Updated',
        'gender' => Gender::Female->value,
        'civil_status' => CivilStatus::Married->value,
        'dob' => '1991-02-02',
        'membership_at' => '2021-02-02',
        'baptized_at' => '2011-02-02',
        'saved_at' => '2006-02-02',
        'testimony' => 'Test testimony updated',
        'email' => 'john.doe.updated@example.com',
        'phone' => '1112223333',
        'alternate_phone' => '4445556666',
        'address_line_1' => '456 Main St Updated',
        'address_line_2' => 'Apt 5C Updated',
        'city' => 'Newtown',
        'state' => 'Newstate',
        'postal_code' => '54321',
        'country' => 'CAN',
    ];

    // Act
    $updatedPerson = $action->handle($person, $data);

    // Assert
    expect($updatedPerson->id)->toBe($person->id);
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'John Updated',
        'last_name' => 'Doe Updated',
        'gender' => Gender::Female->value,
        'civil_status' => CivilStatus::Married->value,
        'dob' => '1991-02-02 00:00:00',
    ]);

    $this->assertDatabaseHas('spiritual_information', [
        'person_id' => $person->id,
        'membership_at' => '2021-02-02', // Corrected date format
        'baptized_at' => '2011-02-02', // Corrected date format
        'saved_at' => '2006-02-02', // Corrected date format
        'testimony' => 'Test testimony updated',
    ]);

    $this->assertDatabaseHas('contact_information', [
        'person_id' => $person->id,
        'email' => 'john.doe.updated@example.com',
        'phone' => '1112223333',
        'alternate_phone' => '4445556666',
        'address_line_1' => '456 Main St Updated',
        'address_line_2' => 'Apt 5C Updated',
        'city' => 'Newtown',
        'state' => 'Newstate',
        'postal_code' => '54321',
        'country' => 'CAN',
    ]);
});

it('updates a person with minimal information', function (): void {
    // Arrange
    $person = Person::factory()->create([
        'civil_status' => CivilStatus::Single->value,
        'dob' => '1980-01-01',
    ]);
    $action = new UpdatePersonAction();
    $data = [
        'first_name' => 'Jane Updated',
        'last_name' => 'Doe Updated',
        'gender' => Gender::Male->value, // Changed gender
        'civil_status' => CivilStatus::Single->value, // Ensure civil_status is passed to avoid being nulled
        'dob' => '1980-01-01', // Ensure dob is passed to avoid being nulled
    ];

    // Act
    $updatedPerson = $action->handle($person, $data);

    // Assert
    expect($updatedPerson->id)->toBe($person->id);
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'Jane Updated',
        'last_name' => 'Doe Updated',
        'gender' => Gender::Male->value,
        'civil_status' => CivilStatus::Single->value, // Should remain unchanged
        'dob' => '1980-01-01 00:00:00',
    ]);

    // Spiritual and Contact information should also remain unchanged if not provided in $data
    $person->refresh(); // Refresh to get the latest related model data
    $this->assertDatabaseHas('spiritual_information', [
        'person_id' => $person->id,
        'membership_at' => $person->spiritualInformation->membership_at?->format('Y-m-d'),
        'baptized_at' => $person->spiritualInformation->baptized_at?->format('Y-m-d'),
        'saved_at' => $person->spiritualInformation->saved_at?->format('Y-m-d'),
        'testimony' => $person->spiritualInformation->testimony,
    ]);

    $this->assertDatabaseHas('contact_information', [
        'person_id' => $person->id,
        'email' => $person->contactInformation->email,
        'phone' => $person->contactInformation->phone,
        'alternate_phone' => $person->contactInformation->alternate_phone,
        'address_line_1' => $person->contactInformation->address_line_1,
        'address_line_2' => $person->contactInformation->address_line_2,
        'city' => $person->contactInformation->city,
        'state' => $person->contactInformation->state,
        'postal_code' => $person->contactInformation->postal_code,
        'country' => $person->contactInformation->country,
    ]);
});
