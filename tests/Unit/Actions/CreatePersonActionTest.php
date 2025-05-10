<?php

declare(strict_types=1);

use App\Actions\CreatePersonAction;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a person with all information', function (): void {
    // Arrange
    $action = new CreatePersonAction();
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => Gender::Male->value,
        'civil_status' => CivilStatus::Single->value,
        'dob' => '1990-01-01',
        'membership_at' => '2020-01-01',
        'baptized_at' => '2010-01-01',
        'saved_at' => '2005-01-01',
        'testimony' => 'Test testimony',
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
        'alternate_phone' => '0987654321',
        'address_line_1' => '123 Main St',
        'address_line_2' => 'Apt 4B',
        'city' => 'Anytown',
        'state' => 'Anystate',
        'postal_code' => '12345',
        'country' => 'USA',
    ];

    // Act
    $person = $action->handle($data);

    // Assert
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => Gender::Male->value,
        'civil_status' => CivilStatus::Single->value,
        'dob' => '1990-01-01 00:00:00',
    ]);

    $this->assertDatabaseHas('spiritual_information', [
        'person_id' => $person->id,
        'membership_at' => '2020-01-01 00:00:00',
        'baptized_at' => '2010-01-01 00:00:00',
        'saved_at' => '2005-01-01 00:00:00',
        'testimony' => 'Test testimony',
    ]);

    $this->assertDatabaseHas('contact_information', [
        'person_id' => $person->id,
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
        'alternate_phone' => '0987654321',
        'address_line_1' => '123 Main St',
        'address_line_2' => 'Apt 4B',
        'city' => 'Anytown',
        'state' => 'Anystate',
        'postal_code' => '12345',
        'country' => 'USA',
    ]);
});

it('creates a person with minimal information', function (): void {
    // Arrange
    $action = new CreatePersonAction();
    $data = [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'gender' => Gender::Female->value,
    ];

    // Act
    $person = $action->handle($data);

    // Assert
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'gender' => Gender::Female->value,
        'civil_status' => null,
        'dob' => null,
    ]);

    $this->assertDatabaseHas('spiritual_information', [
        'person_id' => $person->id,
        'membership_at' => null,
        'baptized_at' => null,
        'saved_at' => null,
        'testimony' => null,
    ]);

    $this->assertDatabaseHas('contact_information', [
        'person_id' => $person->id,
        'email' => null,
        'phone' => null,
        'alternate_phone' => null,
        'address_line_1' => null,
        'address_line_2' => null,
        'city' => null,
        'state' => null,
        'postal_code' => null,
        'country' => null,
    ]);
});
