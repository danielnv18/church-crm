<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Enums\PermissionModelAction;
use App\Models\Person;
use App\Models\User;
use App\Policies\PersonPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// viewAny method tests
test('viewAny returns true when user has permission', function () {
    // Create a user with permission to view any people
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::ViewAny->value.' person']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that viewAny returns true
    expect($policy->viewAny($user))->toBeTrue();
});

test('viewAny returns false when user does not have permission', function () {
    // Create a user without permission to view any people
    $user = User::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that viewAny returns false
    expect($policy->viewAny($user))->toBeFalse();
});

// view method tests
test('view returns true when user has permission', function () {
    // Create a user with permission to view people
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::View->value.' person']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a person to view
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that view returns true
    expect($policy->view($user, $person))->toBeTrue();
});

test('view returns false when user does not have permission', function () {
    // Create a user without permission to view people
    $user = User::factory()->create();

    // Create a person to view
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that view returns false
    expect($policy->view($user, $person))->toBeFalse();
});

// create method tests
test('create returns true when user has permission', function () {
    // Create a user with permission to create people
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' person']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that create returns true
    expect($policy->create($user))->toBeTrue();
});

test('create returns false when user does not have permission', function () {
    // Create a user without permission to create people
    $user = User::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that create returns false
    expect($policy->create($user))->toBeFalse();
});

// update method tests
test('update returns true when user has permission', function () {
    // Create a user with permission to update people
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' person']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a person to update
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that update returns true
    expect($policy->update($user, $person))->toBeTrue();
});

test('update returns false when user does not have permission', function () {
    // Create a user without permission to update people
    $user = User::factory()->create();

    // Create a person to update
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that update returns false
    expect($policy->update($user, $person))->toBeFalse();
});

// delete method tests
test('delete returns true when user has permission', function () {
    // Create a user with permission to delete people
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Delete->value.' person']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a person to delete
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that delete returns true
    expect($policy->delete($user, $person))->toBeTrue();
});

test('delete returns false when user does not have permission', function () {
    // Create a user without permission to delete people
    $user = User::factory()->create();

    // Create a person to delete
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that delete returns false
    expect($policy->delete($user, $person))->toBeFalse();
});

// restore method tests
test('restore returns true when user has permission', function () {
    // Create a user with permission to restore people
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Restore->value.' person']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a person to restore
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that restore returns true
    expect($policy->restore($user, $person))->toBeTrue();
});

test('restore returns false when user does not have permission', function () {
    // Create a user without permission to restore people
    $user = User::factory()->create();

    // Create a person to restore
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that restore returns false
    expect($policy->restore($user, $person))->toBeFalse();
});

// forceDelete method tests
test('forceDelete returns true when user has permission', function () {
    // Create a user with permission to force delete people
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::ForceDelete->value.' person']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a person to force delete
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that forceDelete returns true
    expect($policy->forceDelete($user, $person))->toBeTrue();
});

test('forceDelete returns false when user does not have permission', function () {
    // Create a user without permission to force delete people
    $user = User::factory()->create();

    // Create a person to force delete
    $person = Person::factory()->create();

    // Create a policy instance
    $policy = new PersonPolicy();

    // Assert that forceDelete returns false
    expect($policy->forceDelete($user, $person))->toBeFalse();
});
