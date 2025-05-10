<?php

declare(strict_types=1);

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can authenticate with remember me checked', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('login fails with missing email', function () {
    $response = $this->post('/login', [
        'password' => 'password',
    ]);
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login fails with invalid email format', function () {
    $response = $this->post('/login', [
        'email' => 'not-an-email',
        'password' => 'password',
    ]);
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login fails with missing password', function () {
    $user = User::factory()->create();
    $response = $this->post('/login', [
        'email' => $user->email,
    ]);
    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});
