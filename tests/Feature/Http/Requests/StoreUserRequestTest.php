<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests;

use App\Enums\PermissionModelAction;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

// Authorization tests
test('authorize returns true when user can create users', function () {
    // Create a user with permission to create users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Mock Auth facade to return our user
    Auth::shouldReceive('user')->andReturn($user);

    // Create a request instance
    $request = new StoreUserRequest();

    // Assert that authorize returns true
    expect($request->authorize())->toBeTrue();
});

test('authorize returns false when user cannot create users', function () {
    // Create a user without permission to create users
    $user = User::factory()->create();

    // Mock Auth facade to return our user
    Auth::shouldReceive('user')->andReturn($user);

    // Create a request instance
    $request = new StoreUserRequest();

    // Assert that authorize returns false
    expect($request->authorize())->toBeFalse();
});

test('authorize returns false when user is not authenticated', function () {
    // Mock Auth facade to return null (unauthenticated)
    Auth::shouldReceive('user')->andReturn(null);

    // Create a request instance
    $request = new StoreUserRequest();

    // Assert that authorize returns false
    expect($request->authorize())->toBeFalse();
});

// Validation rules tests
test('rules method returns the correct validation rules', function () {
    // Create a request instance
    $request = new StoreUserRequest();

    // Get the rules
    $rules = $request->rules();

    // Assert that the rules array contains the expected keys
    expect($rules)->toHaveKeys(['name', 'email', 'password', 'role_ids', 'role_ids.*']);

    // Assert specific rules for each field
    expect($rules['name'])->toContain('required')
        ->toContain('string')
        ->toContain('max:255');

    expect($rules['email'])->toContain('required')
        ->toContain('string')
        ->toContain('lowercase')
        ->toContain('email')
        ->toContain('max:255')
        ->toContain('unique:users,email');

    expect($rules['password'])->toContain('required')
        ->toContain('confirmed');

    // Password rule is an instance of Password class, so we can't directly check it
    // But we can verify it's in the array
    expect($rules['password'])->toHaveCount(3);

    // Assert role_ids is required and must be an array
    expect($rules['role_ids'])->toContain('required')
        ->toContain('array');

    // Assert each role_id must exist in the roles table
    expect($rules['role_ids.*'])->toContain('required')
        ->toContain('exists:roles,id');
});

// Validation behavior tests
test('validation fails when required fields are missing', function () {
    // Create a user with permission to create users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Make a request with missing required fields
    $response = $this->actingAs($user)
        ->post(route('users.store'), [
            // Missing name, email, password, and role_ids
        ]);

    // Assert validation fails for required fields
    $response->assertSessionHasErrors(['name', 'email', 'password', 'role_ids']);
});

test('validation fails when role_ids is not provided', function () {
    // Create a user with permission to create users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Make a request with all required fields except role_ids
    $response = $this->actingAs($user)
        ->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // Missing role_ids
        ]);

    // Assert validation fails for role_ids
    $response->assertSessionHasErrors(['role_ids']);
});

test('validation fails when role_ids is empty array', function () {
    // Create a user with permission to create users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Make a request with all required fields but role_ids is an empty array
    $response = $this->actingAs($user)
        ->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_ids' => [], // Empty array
        ]);

    // Assert validation fails for role_ids
    $response->assertSessionHasErrors(['role_ids']);
});

test('validation passes with valid data including role_ids', function () {
    // Create a user with permission to create users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a role for assignment
    $userRole = Role::create(['name' => 'user']);

    // Make a request with all required fields and valid data
    $response = $this->actingAs($user)
        ->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_ids' => [$userRole->id],
        ]);

    // Assert validation passes (no validation errors for these fields)
    $response->assertSessionDoesntHaveErrors(['name', 'email', 'password', 'role_ids']);
});
