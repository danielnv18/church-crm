<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;

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

test('users are rate limited after too many failed login attempts', function () {
    Event::fake();
    $user = User::factory()->create();

    $throttleKey = Str::transliterate(Str::lower($user->email).'|127.0.0.1');

    // Simulate 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    // The 6th attempt should be rate limited
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['email' => __('auth.throttle', ['seconds' => 60, 'minutes' => 1])]);
    Event::assertDispatched(Lockout::class);
    $this->assertGuest();

    // Clear the rate limiter for subsequent tests if necessary, or advance time
    RateLimiter::clear($throttleKey);
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
