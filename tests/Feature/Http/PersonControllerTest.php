<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Enums\PermissionModelAction;
use App\Enums\RoleEnum;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

// Index method tests
test('index method returns people list for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $permission = Permission::create(['name' => PermissionModelAction::ViewAny->value.' person']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create some people for the list
    Person::factory()->count(3)->create();

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.index'));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('people/index')
            ->has('people', 3)
        );
});

test('index method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.index'));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('index method redirects unauthenticated user to login', function () {
    // Make the request without authentication
    $response = $this->get(route('people.index'));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Create method tests
test('create method returns form for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' person']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.create'));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('people/create')
        );
});

test('create method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.create'));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('create method redirects unauthenticated user to login', function () {
    // Make the request without authentication
    $response = $this->get(route('people.create'));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Store method tests
test('store method creates a new person for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' person']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create person data
    $personData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => 'male',
        'civil_status' => 'single',
        'dob' => '1990-01-01',
    ];

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->post(route('people.store'), $personData);

    // Assert the response redirects to the index page with a success message
    $response->assertRedirect(route('people.index'))
        ->assertSessionHas('success', 'Person created successfully.');

    // Assert the person was created in the database
    $this->assertDatabaseHas('people', [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

test('store method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Create person data
    $personData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => 'male',
        'civil_status' => 'single',
        'dob' => '1990-01-01',
    ];

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->post(route('people.store'), $personData);

    // Assert the response is forbidden
    $response->assertForbidden();

    // Assert the person was not created in the database
    $this->assertDatabaseMissing('people', [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

test('store method redirects unauthenticated user to login', function () {
    // Create person data
    $personData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => 'male',
        'civil_status' => 'single',
        'dob' => '1990-01-01',
    ];

    // Make the request without authentication
    $response = $this->post(route('people.store'), $personData);

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));

    // Assert the person was not created in the database
    $this->assertDatabaseMissing('people', [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

// Show method tests
test('show method returns person details for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $permission = Permission::create(['name' => PermissionModelAction::View->value.' person']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create a person
    $person = Person::factory()->create();

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.show', $person));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('people/show')
            ->has('person')
            ->where('person.id', $person->id)
        );
});

test('show method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Create a person
    $person = Person::factory()->create();

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.show', $person));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('show method redirects unauthenticated user to login', function () {
    // Create a person
    $person = Person::factory()->create();

    // Make the request without authentication
    $response = $this->get(route('people.show', $person));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Edit method tests
test('edit method returns form for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' person']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create a person
    $person = Person::factory()->create();

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.edit', $person));

    // Assert the response is successful and returns the correct view
    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('people/edit')
            ->has('person')
            ->where('person.id', $person->id)
        );
});

test('edit method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Create a person
    $person = Person::factory()->create();

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->get(route('people.edit', $person));

    // Assert the response is forbidden
    $response->assertForbidden();
});

test('edit method redirects unauthenticated user to login', function () {
    // Create a person
    $person = Person::factory()->create();

    // Make the request without authentication
    $response = $this->get(route('people.edit', $person));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));
});

// Update method tests
test('update method updates a person for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' person']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create a person
    $person = Person::factory()->create([
        'first_name' => 'Original',
        'last_name' => 'Name',
    ]);

    // Create updated person data
    $updatedData = [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'gender' => 'male',
        'civil_status' => 'single',
        'dob' => '1990-01-01',
    ];

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), $updatedData);

    // Assert the response redirects to the index page with a success message
    $response->assertRedirect(route('people.index'))
        ->assertSessionHas('success', 'Person updated successfully.');

    // Assert the person was updated in the database
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'Updated',
    ]);
});

test('update method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Create a person
    $person = Person::factory()->create([
        'first_name' => 'Original',
        'last_name' => 'Name',
    ]);

    // Create updated person data
    $updatedData = [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'gender' => 'male',
        'civil_status' => 'single',
        'dob' => '1990-01-01',
    ];

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->put(route('people.update', $person), $updatedData);

    // Assert the response is forbidden
    $response->assertForbidden();

    // Assert the person was not updated in the database
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'Original',
    ]);
});

test('update method redirects unauthenticated user to login', function () {
    // Create a person
    $person = Person::factory()->create([
        'first_name' => 'Original',
        'last_name' => 'Name',
    ]);

    // Create updated person data
    $updatedData = [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'gender' => 'male',
        'civil_status' => 'single',
        'dob' => '1990-01-01',
    ];

    // Make the request without authentication
    $response = $this->put(route('people.update', $person), $updatedData);

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));

    // Assert the person was not updated in the database
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'first_name' => 'Original',
    ]);
});

// Destroy method tests
test('destroy method deletes a person for authorized user', function () {
    // Create a role with the necessary permission
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $permission = Permission::create(['name' => PermissionModelAction::Delete->value.' person']);
    $role->givePermissionTo($permission);

    // Create a user with the role
    $user = User::factory()->create();
    $user->assignRole($role);

    // Create a person
    $person = Person::factory()->create();

    // Act as the authorized user and make the request
    $response = $this->actingAs($user)
        ->delete(route('people.destroy', $person));

    // Assert the response redirects to the index page with a success message
    $response->assertRedirect(route('people.index'))
        ->assertSessionHas('success', 'Person deleted successfully.');

    // Assert the person was soft deleted in the database
    $this->assertSoftDeleted('people', [
        'id' => $person->id,
    ]);
});

test('destroy method returns 403 for unauthorized user', function () {
    // Create a user without the necessary permission
    $user = User::factory()->create();

    // Create a person
    $person = Person::factory()->create();

    // Act as the unauthorized user and make the request
    $response = $this->actingAs($user)
        ->delete(route('people.destroy', $person));

    // Assert the response is forbidden
    $response->assertForbidden();

    // Assert the person was not deleted in the database
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'deleted_at' => null,
    ]);
});

test('destroy method redirects unauthenticated user to login', function () {
    // Create a person
    $person = Person::factory()->create();

    // Make the request without authentication
    $response = $this->delete(route('people.destroy', $person));

    // Assert the response redirects to login
    $response->assertRedirect(route('login'));

    // Assert the person was not deleted in the database
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'deleted_at' => null,
    ]);
});
