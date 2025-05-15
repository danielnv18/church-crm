<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Enums\PermissionModelAction;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// viewAny method tests
test('viewAny returns true when user has permission', function () {
    // Create a user with permission to view any users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::ViewAny->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that viewAny returns true
    expect($policy->viewAny($user))->toBeTrue();
});

test('viewAny returns false when user does not have permission', function () {
    // Create a user without permission to view any users
    $user = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that viewAny returns false
    expect($policy->viewAny($user))->toBeFalse();
});

// view method tests
test('view returns true when user is viewing their own profile', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that view returns true when viewing own profile
    expect($policy->view($user, $user))->toBeTrue();
});

test('view returns true when user has permission to view any user', function () {
    // Create a user with permission to view any users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::ViewAny->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create another user to view
    $otherUser = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that view returns true
    expect($policy->view($user, $otherUser))->toBeTrue();
});

test('view returns false when user does not have permission and is not viewing own profile', function () {
    // Create two users without specific permissions
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that view returns false
    expect($policy->view($user1, $user2))->toBeFalse();
});

// create method tests
test('create returns true when user has permission', function () {
    // Create a user with permission to create users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Create->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that create returns true
    expect($policy->create($user))->toBeTrue();
});

test('create returns false when user does not have permission', function () {
    // Create a user without permission to create users
    $user = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that create returns false
    expect($policy->create($user))->toBeFalse();
});

// update method tests
test('update returns true when user is updating their own profile', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that update returns true when updating own profile
    expect($policy->update($user, $user))->toBeTrue();
});

test('update returns true when user has permission to update any user', function () {
    // Create a user with permission to update users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Update->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create another user to update
    $otherUser = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that update returns true
    expect($policy->update($user, $otherUser))->toBeTrue();
});

test('update returns false when user does not have permission and is not updating own profile', function () {
    // Create two users without specific permissions
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that update returns false
    expect($policy->update($user1, $user2))->toBeFalse();
});

// delete method tests
test('delete returns true when user is deleting their own profile', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that delete returns true when deleting own profile
    expect($policy->delete($user, $user))->toBeTrue();
});

test('delete returns true when user has permission to delete any user', function () {
    // Create a user with permission to delete users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Delete->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create another user to delete
    $otherUser = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that delete returns true
    expect($policy->delete($user, $otherUser))->toBeTrue();
});

test('delete returns false when user does not have permission and is not deleting own profile', function () {
    // Create two users without specific permissions
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that delete returns false
    expect($policy->delete($user1, $user2))->toBeFalse();
});

// restore method tests
test('restore returns true when user has permission', function () {
    // Create a user with permission to restore users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::Restore->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create another user to restore
    $otherUser = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that restore returns true
    expect($policy->restore($user, $otherUser))->toBeTrue();
});

test('restore returns false when user does not have permission', function () {
    // Create a user without permission to restore users
    $user = User::factory()->create();

    // Create another user to restore
    $otherUser = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that restore returns false
    expect($policy->restore($user, $otherUser))->toBeFalse();
});

// forceDelete method tests
test('forceDelete returns true when user has permission', function () {
    // Create a user with permission to force delete users
    $user = User::factory()->create();
    $permission = Permission::create(['name' => PermissionModelAction::ForceDelete->value.' user']);
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    // Create another user to force delete
    $otherUser = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that forceDelete returns true
    expect($policy->forceDelete($user, $otherUser))->toBeTrue();
});

test('forceDelete returns false when user does not have permission', function () {
    // Create a user without permission to force delete users
    $user = User::factory()->create();

    // Create another user to force delete
    $otherUser = User::factory()->create();

    // Create a policy instance
    $policy = new UserPolicy();

    // Assert that forceDelete returns false
    expect($policy->forceDelete($user, $otherUser))->toBeFalse();
});
