<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Enums\PermissionModelAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

// Index method tests
test('index method returns users list for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::ViewAny->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create some additional users for the list
    User::factory()->count(3)->create();

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('users.index'));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->has('users', 4) // 4 users total (including the authenticated user)
        );
});

test('index method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('users.index'));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('index method redirects unauthenticated user to login', function () {
    // Make the request without authentication
    $response = $this->get(route('users.index'));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Create method tests
test('create method returns form for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('users.create'));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/create')
            ->has('roles')
        );
});

test('create method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('users.create'));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('create method redirects unauthenticated user to login', function () {
    // Make the request without authentication
    $response = $this->get(route('users.create'));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Store method tests
test('store method creates a new user for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create a user role for assignment
    $userRole = Role::create(['name' => 'user']);

    // Prepare data for the new user
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_ids' => [$userRole->id],
    ];

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->post(route('users.store'), $userData);

    // Assert the response redirects to the users index with a success message
    $response->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'User created successfully.');

    // Assert the user was created in the database
    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
    ]);

    // Assert the role was assigned
    $newUser = User::where('email', 'newuser@example.com')->first();
    $this->assertTrue($newUser->hasRole('user'));
});

test('store method validates input data', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Prepare invalid data (missing required fields)
    $userData = [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'different',
        'role_ids' => [],
    ];

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->post(route('users.store'), $userData);

    // Assert the response has validation errors
    $response->assertSessionHasErrors(['name', 'email', 'password', 'role_ids']);

    // Assert no user was created
    $this->assertDatabaseMissing('users', [
        'email' => 'not-an-email',
    ]);
});

test('store method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Prepare data for the new user
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_ids' => [1],
    ];

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->post(route('users.store'), $userData);

    // Assert the response is forbidden
    $response->assertForbidden();

    // Assert no user was created
    $this->assertDatabaseMissing('users', [
        'email' => 'newuser@example.com',
    ]);
});

test('store method redirects unauthenticated user to login', function () {
    // Prepare data for the new user
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_ids' => [1],
    ];

    // Make the request without authentication
    $response = $this->post(route('users.store'), $userData);

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));

    // Assert no user was created
    $this->assertDatabaseMissing('users', [
        'email' => 'newuser@example.com',
    ]);
});

// Show method tests
test('show method displays user details for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::ViewAny->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $admin = User::factory()->create();
    $admin->assignRole($role);

    // Create a user to view
    $user = User::factory()->create([
        'name' => 'View User',
        'email' => 'viewuser@example.com',
    ]);
    $user->assignRole('admin');

    // Act as the authorized user and make the request
    $response = $this->actingAs($admin)
        ->get(route('users.show', $user));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/show')
            ->has('user', fn (Assert $user) => $user
                ->where('name', 'View User')
                ->where('email', 'viewuser@example.com')
                ->etc()
            )
        );
});

test('show method allows user to view their own profile', function () {
    // Create a user
    $user = User::factory()->create([
        'name' => 'Self View',
        'email' => 'selfview@example.com',
    ]);

    // Act as the user and make the request to view their own profile
    $response = $this->actingAs($user)
        ->get(route('users.show', $user));

    // Assert the response is successful
    $response->assertOk();
});

test('show method returns 403 for unauthorized user', function () {
    // Create two users without the necessary permission
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Act as user1 and try to view user2's profile
    $response = $this->actingAs($user1)
        ->get(route('users.show', $user2));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('show method redirects unauthenticated user to login', function () {
    // Create a user
    $user = User::factory()->create();

    // Make the request without authentication
    $response = $this->get(route('users.show', $user));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Edit method tests
test('edit method returns form for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $admin = User::factory()->create();
    $admin->assignRole($role);

    // Create a user to edit
    $user = User::factory()->create([
        'name' => 'Edit User',
        'email' => 'edituser@example.com',
    ]);

    // Act as the authorized user and make the request
    $response = $this->actingAs($admin)
        ->get(route('users.edit', $user));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/edit')
            ->has('user', fn (Assert $user) => $user
                ->where('name', 'Edit User')
                ->where('email', 'edituser@example.com')
                ->etc()
            )
            ->has('roles')
        );
});

test('edit method allows user to edit their own profile', function () {
    // Create a user
    $user = User::factory()->create([
        'name' => 'Self Edit',
        'email' => 'selfedit@example.com',
    ]);

    // Act as the user and make the request to edit their own profile
    $response = $this->actingAs($user)
        ->get(route('users.edit', $user));

    // Assert the response is successful
    $response->assertOk();
});

test('edit method returns 403 for unauthorized user', function () {
    // Create two users without the necessary permission
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Act as user1 and try to edit user2's profile
    $response = $this->actingAs($user1)
        ->get(route('users.edit', $user2));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('edit method redirects unauthenticated user to login', function () {
    // Create a user
    $user = User::factory()->create();

    // Make the request without authentication
    $response = $this->get(route('users.edit', $user));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Update method tests
test('update method updates user for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $admin = User::factory()->create();
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    // Create a user role for assignment
    $userRole = Role::create(['name' => 'user']);

    // Prepare update data
    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role_ids' => [$userRole->id],
    ];

    // Act as the authorized user and make the request
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), $updateData);

    // Assert the response redirects to the users index with a success message
    $response->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'User updated successfully.');

    // Assert the user was updated in the database
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    // Assert the role was assigned
    $updatedUser = User::find($user->id);
    $this->assertTrue($updatedUser->hasRole('user'));
});

test('update method allows user to update their own profile', function () {
    // Create a user
    $user = User::factory()->create([
        'name' => 'Self Update',
        'email' => 'selfupdate@example.com',
    ]);

    // Create a role for assignment
    $role = Role::create(['name' => 'user']);

    // Prepare update data
    $updateData = [
        'name' => 'Updated Self',
        'email' => 'updatedself@example.com',
        'role_ids' => [$role->id],
    ];

    // Act as the user and make the request to update their own profile
    $response = $this->actingAs($user)
        ->put(route('users.update', $user), $updateData);

    // Assert the response redirects with a success message
    $response->assertRedirect()
        ->assertSessionHas('success');

    // Assert the user was updated
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Self',
        'email' => 'updatedself@example.com',
    ]);
});

test('update method validates input data', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $admin = User::factory()->create();
    $admin->assignRole($role);

    // Create a user to update
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    // Prepare invalid update data
    $updateData = [
        'name' => '',
        'email' => 'not-an-email',
        'role_ids' => [],
    ];

    // Act as the authorized user and make the request
    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), $updateData);

    // Assert the response has validation errors
    $response->assertSessionHasErrors(['name', 'email', 'role_ids']);

    // Assert the user was not updated
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
});

test('update method returns 403 for unauthorized user', function () {
    // Create two users without the necessary permission
    $user1 = User::factory()->create();
    $user2 = User::factory()->create([
        'name' => 'Protected User',
        'email' => 'protected@example.com',
    ]);

    // Prepare update data
    $updateData = [
        'name' => 'Hacked Name',
        'email' => 'hacked@example.com',
        'role_ids' => [1],
    ];

    // Act as user1 and try to update user2's profile
    $response = $this->actingAs($user1)
        ->put(route('users.update', $user2), $updateData);

    // Assert the response is forbidden
    $response->assertForbidden();

    // Assert the user was not updated
    $this->assertDatabaseHas('users', [
        'id' => $user2->id,
        'name' => 'Protected User',
        'email' => 'protected@example.com',
    ]);
});

test('update method redirects unauthenticated user to login', function () {
    // Create a user
    $user = User::factory()->create([
        'name' => 'Protected User',
        'email' => 'protected@example.com',
    ]);

    // Prepare update data
    $updateData = [
        'name' => 'Hacked Name',
        'email' => 'hacked@example.com',
        'role_ids' => [1],
    ];

    // Make the request without authentication
    $response = $this->put(route('users.update', $user), $updateData);

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));

    // Assert the user was not updated
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Protected User',
        'email' => 'protected@example.com',
    ]);
});

// Destroy method tests
test('destroy method deletes user for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => PermissionModelAction::Delete->value.' user']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $admin = User::factory()->create();
    $admin->assignRole($role);

    // Create a user to delete
    $user = User::factory()->create([
        'name' => 'Delete User',
        'email' => 'deleteuser@example.com',
    ]);

    // Act as the authorized user and make the request
    $response = $this->actingAs($admin)
        ->delete(route('users.destroy', $user));

    // Assert the response redirects to the users index with a success message
    $response->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'User deleted successfully.');

    // Assert the user was deleted from the database
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('destroy method allows user to delete their own profile', function () {
    // Create a user
    $user = User::factory()->create([
        'name' => 'Self Delete',
        'email' => 'selfdelete@example.com',
    ]);

    // Act as the user and make the request to delete their own profile
    $response = $this->actingAs($user)
        ->delete(route('users.destroy', $user));

    // Assert the response redirects with a success message
    $response->assertRedirect()
        ->assertSessionHas('success');

    // Assert the user was deleted
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('destroy method returns 403 for unauthorized user', function () {
    // Create two users without the necessary permission
    $user1 = User::factory()->create();
    $user2 = User::factory()->create([
        'name' => 'Protected User',
        'email' => 'protected@example.com',
    ]);

    // Act as user1 and try to delete user2's profile
    $response = $this->actingAs($user1)
        ->delete(route('users.destroy', $user2));

    // Assert the response is forbidden
    $response->assertForbidden();

    // Assert the user was not deleted
    $this->assertDatabaseHas('users', [
        'id' => $user2->id,
    ]);
});

test('destroy method redirects unauthenticated user to login', function () {
    // Create a user
    $user = User::factory()->create([
        'name' => 'Protected User',
        'email' => 'protected@example.com',
    ]);

    // Make the request without authentication
    $response = $this->delete(route('users.destroy', $user));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));

    // Assert the user was not deleted
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});
