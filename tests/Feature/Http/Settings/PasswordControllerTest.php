<?php

declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('password settings page can be rendered by authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('password.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('settings/password'));
});

test('password settings page redirects unauthenticated user to login', function () {
    $this->get(route('password.edit'))
        ->assertRedirect(route('login'));
});

test('user can update their password with valid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($user)->put(route('password.update'), [
        'current_password' => 'old-password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
    expect(Hash::check('new-secure-password', $user->fresh()->password))->toBeTrue();
});

test('user cannot update password with invalid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($user)->put(route('password.update'), [
        'current_password' => 'wrong-current-password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ]);

    $response->assertSessionHasErrors('current_password');
    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('user cannot update password if new password confirmation does not match', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($user)->put(route('password.update'), [
        'current_password' => 'old-password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'mismatched-password',
    ]);

    $response->assertSessionHasErrors('password');
    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('unauthenticated user cannot update password', function () {
    $response = $this->put(route('password.update'), [
        'current_password' => 'old-password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ]);

    $response->assertRedirect(route('login'));
});
