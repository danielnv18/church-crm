<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('email verification prompt is shown to unverified user', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('auth/verify-email'));
});

test('email verification prompt redirects verified user to dashboard', function () {
    $user = User::factory()->create(); // Verified by default

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertRedirect(route('dashboard', absolute: false));
});

test('email verification prompt redirects unauthenticated user to login', function () {
    $this->get(route('verification.notice'))
        ->assertRedirect(route('login'));
});

test('email verification prompt shows status message', function () {
    $user = User::factory()->unverified()->create();
    $status = 'verification-link-sent';

    $this->actingAs($user)
        ->withSession(['status' => $status])
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/verify-email')
            ->where('status', $status)
        );
});
