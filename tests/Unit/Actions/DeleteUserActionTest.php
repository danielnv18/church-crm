<?php

declare(strict_types=1);

use App\Actions\DeleteUserAction;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes a user from the database', function (): void {
    // Arrange
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);

    $action = new DeleteUserAction();

    // Act
    $action->handle($user);

    // Assert
    $this->assertDatabaseMissing('users', [
        'email' => 'test@example.com',
    ]);

    $this->assertNull(User::find($user->id));
});

it('deletes a user with roles', function (): void {
    // Arrange
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $user = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
    ]);

    // Assign a role to the user
    $user->assignRole($role->name);

    $this->assertTrue($user->hasRole('admin'));
    $this->assertDatabaseHas('users', [
        'email' => 'admin@example.com',
    ]);

    $action = new DeleteUserAction();

    // Act
    $action->handle($user);

    // Assert
    $this->assertDatabaseMissing('users', [
        'email' => 'admin@example.com',
    ]);

    $this->assertNull(User::find($user->id));
});
