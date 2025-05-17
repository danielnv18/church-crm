<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests;

use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

// Authorization tests
// Note: UpdatePersonRequest currently returns true for all users, but we'll test the future behavior
test('authorize returns true for all users currently', function () {
    // Create a user
    $user = User::factory()->create();

    // Mock Auth facade to return our user
    Auth::shouldReceive('user')->andReturn($user);

    // Create a request instance
    $request = new UpdatePersonRequest();

    // Assert that authorize returns true
    expect($request->authorize())->toBeTrue();
});

// Validation rules tests
test('rules method returns the correct validation rules', function () {
    // Create a request instance
    $request = new UpdatePersonRequest();

    // Get the rules
    $rules = $request->rules();

    // Assert that the rules array contains the expected keys
    expect($rules)->toHaveKeys([
        'first_name', 'last_name', 'gender', 'civil_status', 'dob',
        'membership_at', 'baptized_at', 'saved_at', 'testimony',
        'email', 'phone', 'alternate_phone', 'address_line_1', 'address_line_2',
        'city', 'state', 'postal_code', 'country',
    ]);

    // Assert specific rules for each required field
    expect($rules['first_name'])->toContain('required')
        ->toContain('string')
        ->toContain('max:255');

    expect($rules['last_name'])->toContain('required')
        ->toContain('string')
        ->toContain('max:255');

    expect($rules['gender'])->toContain('required');
    // Gender rule uses Rule::enum which we can't directly check, but we can verify it's in the array
    expect($rules['gender'])->toHaveCount(2);

    // Assert rules for nullable fields
    expect($rules['civil_status'])->toContain('nullable');
    expect($rules['dob'])->toContain('nullable');
    expect($rules['email'])->toContain('nullable')
        ->toContain('email');
    expect($rules['phone'])->toContain('nullable')
        ->toContain('string');
});

// Validation behavior tests
test('validation fails when required fields are missing', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Make a request with missing required fields
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), [
            // Missing first_name, last_name, gender
        ]);

    // Assert validation fails for required fields
    $response->assertSessionHasErrors(['first_name', 'last_name', 'gender']);
});

test('validation fails when gender is invalid', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Make a request with invalid gender
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'invalid-gender', // Invalid gender
        ]);

    // Assert validation fails for gender
    $response->assertSessionHasErrors(['gender']);
});

test('validation fails when civil_status is invalid', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Make a request with invalid civil_status
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => Gender::Male->value,
            'civil_status' => 'invalid-status', // Invalid civil_status
        ]);

    // Assert validation fails for civil_status
    $response->assertSessionHasErrors(['civil_status']);
});

test('validation fails when email is invalid', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Make a request with invalid email
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => Gender::Male->value,
            'email' => 'not-an-email', // Invalid email
        ]);

    // Assert validation fails for email
    $response->assertSessionHasErrors(['email']);
});

test('validation fails when date fields are invalid', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Make a request with invalid dates
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => Gender::Male->value,
            'dob' => 'not-a-date', // Invalid date
            'membership_at' => 'not-a-date', // Invalid date
            'baptized_at' => 'not-a-date', // Invalid date
            'saved_at' => 'not-a-date', // Invalid date
        ]);

    // Assert validation fails for date fields
    $response->assertSessionHasErrors(['dob', 'membership_at', 'baptized_at', 'saved_at']);
});

test('validation passes with valid data', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Make a request with all required fields and valid data
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => Gender::Male->value,
            'civil_status' => CivilStatus::Single->value,
            'dob' => '1990-01-01',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'address_line_1' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'postal_code' => '12345',
            'country' => 'USA',
            'membership_at' => '2020-01-01',
            'baptized_at' => '2019-01-01',
            'saved_at' => '2018-01-01',
            'testimony' => 'This is my testimony.',
        ]);

    // Assert validation passes (no validation errors for these fields)
    $response->assertSessionDoesntHaveErrors([
        'first_name', 'last_name', 'gender', 'civil_status', 'dob',
        'email', 'phone', 'address_line_1', 'city', 'state', 'postal_code', 'country',
        'membership_at', 'baptized_at', 'saved_at', 'testimony',
    ]);
});

test('validation passes with minimal valid data', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Make a request with only required fields
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => Gender::Male->value,
        ]);

    // Assert validation passes for required fields
    $response->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'gender']);
});
