<?php

declare(strict_types=1);

use App\Models\ContactInformation;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can create a contact information with valid attributes', function (): void {
    // Arrange
    $person = Person::factory()->create();

    $data = [
        'person_id' => $person->id,
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
        'alternate_phone' => '0987654321',
        'address_line_1' => '123 Main St',
        'address_line_2' => 'Apt 4B',
        'city' => 'Anytown',
        'state' => 'CA',
        'postal_code' => '12345',
        'country' => 'USA',
    ];

    // Act
    $contactInfo = ContactInformation::create($data);

    // Assert
    expect($contactInfo)->toBeInstanceOf(ContactInformation::class)
        ->and($contactInfo->person_id)->toBe($person->id)
        ->and($contactInfo->email)->toBe('john.doe@example.com')
        ->and($contactInfo->phone)->toBe('1234567890')
        ->and($contactInfo->alternate_phone)->toBe('0987654321')
        ->and($contactInfo->address_line_1)->toBe('123 Main St')
        ->and($contactInfo->address_line_2)->toBe('Apt 4B')
        ->and($contactInfo->city)->toBe('Anytown')
        ->and($contactInfo->state)->toBe('CA')
        ->and($contactInfo->postal_code)->toBe('12345')
        ->and($contactInfo->country)->toBe('USA');

    $this->assertDatabaseHas('contact_information', [
        'person_id' => $person->id,
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
    ]);
});

it('belongs to a person', function (): void {
    // Arrange
    $person = Person::factory()->create();

    $contactInfo = ContactInformation::create([
        'person_id' => $person->id,
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
    ]);

    // Act & Assert
    expect($contactInfo->person)->toBeInstanceOf(Person::class)
        ->and($contactInfo->person->id)->toBe($person->id);
});

it('can be created with minimal attributes', function (): void {
    // Arrange
    $person = Person::factory()->create();

    $data = [
        'person_id' => $person->id,
    ];

    // Act
    $contactInfo = ContactInformation::create($data);

    // Assert
    expect($contactInfo)->toBeInstanceOf(ContactInformation::class)
        ->and($contactInfo->person_id)->toBe($person->id)
        ->and($contactInfo->email)->toBeNull()
        ->and($contactInfo->phone)->toBeNull()
        ->and($contactInfo->alternate_phone)->toBeNull()
        ->and($contactInfo->address_line_1)->toBeNull()
        ->and($contactInfo->address_line_2)->toBeNull()
        ->and($contactInfo->city)->toBeNull()
        ->and($contactInfo->state)->toBeNull()
        ->and($contactInfo->postal_code)->toBeNull()
        ->and($contactInfo->country)->toBeNull();
});

it('has the correct fillable attributes', function (): void {
    // Arrange
    $contactInfo = new ContactInformation();

    // Act & Assert
    expect($contactInfo->getFillable())->toContain('person_id')
        ->toContain('email')
        ->toContain('phone')
        ->toContain('alternate_phone')
        ->toContain('address_line_1')
        ->toContain('address_line_2')
        ->toContain('city')
        ->toContain('state')
        ->toContain('postal_code')
        ->toContain('country');
});
