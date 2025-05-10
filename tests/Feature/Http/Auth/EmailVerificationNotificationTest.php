<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('email verification notification can be sent if user is not verified', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect()
        ->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('email verification notification is not sent if user is already verified', function () {
    Notification::fake();

    $user = User::factory()->create(); // Defaults to verified, or ensure UserFactory has a verified state

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect(route('dashboard', absolute: false));

    Notification::assertNotSentTo($user, VerifyEmail::class);
});

test('unauthenticated user cannot request email verification notification', function () {
    $this->post(route('verification.send'))
        ->assertRedirect(route('login'));
});
