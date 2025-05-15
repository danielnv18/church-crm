<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests;

use App\Enums\PermissionModelAction;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

// Authorization tests
test('user with update permission can update another user', function () {
    // Create a user with permission to update users
    $admin = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create();

    // Make a request to update the user
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role_ids' => [$role->id],
        ]);

    // Assert the request was not forbidden (authorization passed)
    $response->assertStatus(302); // Redirect after successful update
});

test('user can update their own profile', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a role for assignment
    $role = Role::create(['name' => 'user']);

    // Make a request to update their own profile
    $response = $this->actingAs($user)
        ->put(route('users.update', $user), [
            'name' => 'Updated Self',
            'email' => 'updated@example.com',
            'role_ids' => [$role->id],
        ]);

    // Assert the request was not forbidden (authorization passed)
    $response->assertStatus(302); // Redirect after successful update
});

test('user without permission cannot update another user', function () {
    // Create two users without specific permissions
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a role for assignment
    $role = Role::create(['name' => 'user']);

    // Make a request to update another user
    $response = $this->actingAs($user1)
        ->put(route('users.update', $user2), [
            'name' => 'Unauthorized Update',
            'email' => 'unauthorized@example.com',
            'role_ids' => [$role->id],
        ]);

    // Assert the request was forbidden (authorization failed)
    $response->assertForbidden();
});

test('unauthenticated user cannot update any user', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a role for assignment
    $role = Role::create(['name' => 'user']);

    // Make a request without authentication
    $response = $this->put(route('users.update', $user), [
        'name' => 'Unauthorized Update',
        'email' => 'unauthorized@example.com',
        'role_ids' => [$role->id],
    ]);

    // Assert the request redirects to login
    $response->assertRedirect(route('login'));
});

// Validation rules tests
test('rules method returns the correct validation rules', function () {
    // Create a user to update
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Create a request instance
    $request = new UpdateUserRequest();

    // Set the user property on the request
    $request->user = $user;

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
        ->toContain('max:255');

    // Email rule contains a unique rule with ignore, which is an instance of Rule class
    // We can't directly check it, but we can verify the count
    expect($rules['email'])->toHaveCount(6);

    expect($rules['password'])->toContain('nullable')
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
    // Create a user with permission to update users
    $admin = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create();

    // Make a request with missing required fields
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            // Missing name, email, and role_ids
        ]);

    // Assert validation fails for required fields
    $response->assertSessionHasErrors(['name', 'email', 'role_ids']);
});

test('validation fails when role_ids is not provided', function () {
    // Create a user with permission to update users
    $admin = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create();

    // Make a request with all required fields except role_ids
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            // Missing role_ids
        ]);

    // Assert validation fails for role_ids
    $response->assertSessionHasErrors(['role_ids']);
});

test('validation fails when role_ids is empty array', function () {
    // Create a user with permission to update users
    $admin = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create();

    // Make a request with all required fields but role_ids is an empty array
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'role_ids' => [], // Empty array
        ]);

    // Assert validation fails for role_ids
    $response->assertSessionHasErrors(['role_ids']);
});

test('validation passes with valid data including role_ids', function () {
    // Create a user with permission to update users
    $admin = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create();

    // Create a role for assignment
    $userRole = Role::create(['name' => 'user']);

    // Make a request with all required fields and valid data
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'role_ids' => [$userRole->id],
        ]);

    // Assert validation passes (no validation errors for these fields)
    $response->assertSessionDoesntHaveErrors(['name', 'email', 'role_ids']);
});

test('password is optional when updating a user', function () {
    // Create a user with permission to update users
    $admin = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create();

    // Create a role for assignment
    $userRole = Role::create(['name' => 'user']);

    // Make a request with all required fields but without password
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'role_ids' => [$userRole->id],
            // No password provided
        ]);

    // Assert validation passes (no validation errors for password)
    $response->assertSessionDoesntHaveErrors(['password']);
});
