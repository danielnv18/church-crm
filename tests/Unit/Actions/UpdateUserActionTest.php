<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('updates a user with basic information', function (): void {
    // Arrange
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    $role = Role::create(['name' => 'editor']);

    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role_ids' => [$role->id],
    ];

    $action = new UpdateUserAction();

    // Act
    $updatedUser = $action->handle($user, $data);

    // Assert
    expect($updatedUser->name)->toBe('Updated Name')
        ->and($updatedUser->email)->toBe('updated@example.com')
        ->and($updatedUser->hasRole('editor'))->toBeTrue();

    // Verify the changes were persisted to the database
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

it('updates a user with password when provided', function (): void {
    // Arrange
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'password' => bcrypt('old-password'),
    ]);

    $role = Role::create(['name' => 'editor']);

    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'new-password',
        'role_ids' => [$role->id],
    ];

    $action = new UpdateUserAction();

    // Act
    $updatedUser = $action->handle($user, $data);

    // Assert
    expect($updatedUser->name)->toBe('Updated Name')
        ->and($updatedUser->email)->toBe('updated@example.com')
        ->and($updatedUser->hasRole('editor'))->toBeTrue();

    // Verify the password was updated
    $this->assertTrue(Hash::check('new-password', $updatedUser->password));
    $this->assertFalse(Hash::check('old-password', $updatedUser->password));
});

it('syncs user roles', function (): void {
    // Arrange
    $user = User::factory()->create();

    $role1 = Role::create(['name' => 'editor']);
    $role2 = Role::create(['name' => 'admin']);

    // Assign initial role
    $user->assignRole($role1);

    $data = [
        'name' => $user->name,
        'email' => $user->email,
        'role_ids' => [$role2->id], // Change to a different role
    ];

    $action = new UpdateUserAction();

    // Act
    $updatedUser = $action->handle($user, $data);

    // Assert
    expect($updatedUser->hasRole('editor'))->toBeFalse()
        ->and($updatedUser->hasRole('admin'))->toBeTrue();
});
