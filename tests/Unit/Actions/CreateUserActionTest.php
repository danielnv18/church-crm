<?php

declare(strict_types=1);

use App\Actions\CreateUserAction;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a user with roles', function (): void {
    // Arrange
    $role = Role::create(['name' => RoleEnum::Admin->value]);
    $action = new CreateUserAction();
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role_ids' => [$role->id],
    ];

    // Act
    $user = $action->handle($data);

    // Assert
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $dbUser = User::where('email', 'test@example.com')->first();
    expect($dbUser)->not->toBeNull()
        ->and(Hash::check('password123', $dbUser->password))->toBeTrue()
        ->and($dbUser->hasRole('admin'))->toBeTrue()
        ->and($user->id)->toBe($dbUser->id)
        ->and($user->hasRole('admin'))->toBeTrue();
});

it('creates a user without roles if none are provided', function (): void {
    // Arrange
    $action = new CreateUserAction();
    $data = [
        'name' => 'Another User',
        'email' => 'another@example.com',
        'password' => 'securepassword',
        'role_ids' => [], // No roles
    ];

    // Act
    $user = $action->handle($data);

    // Assert
    $this->assertDatabaseHas('users', [
        'name' => 'Another User',
        'email' => 'another@example.com',
    ]);

    $dbUser = User::where('email', 'another@example.com')->first();
    expect($dbUser)->not->toBeNull()
        ->and(Hash::check('securepassword', $dbUser->password))->toBeTrue()
        ->and($dbUser->roles->count())->toBe(0)
        ->and($user->id)->toBe($dbUser->id)
        ->and($user->roles->count())->toBe(0);
});
