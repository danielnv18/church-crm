<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

test('authorize method returns true', function () {
    $request = new LoginRequest();

    expect($request->authorize())->toBeTrue();
});

test('rules method returns expected validation rules', function () {
    $request = new LoginRequest();

    expect($request->rules())->toBe([
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ]);
});

test('throttle key is generated correctly', function () {
    $request = new LoginRequest();
    $request->merge(['email' => 'test@example.com']);
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    expect($request->throttleKey())->toBe('test@example.com|127.0.0.1');
});

test('ensureIsNotRateLimited throws exception when too many attempts', function () {
    Event::fake();

    $request = new LoginRequest();
    $request->merge(['email' => 'test@example.com']);
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    // Mock RateLimiter to simulate too many attempts
    RateLimiter::shouldReceive('tooManyAttempts')
        ->once()
        ->with($request->throttleKey(), 5)
        ->andReturn(true);

    RateLimiter::shouldReceive('availableIn')
        ->once()
        ->with($request->throttleKey())
        ->andReturn(60);

    try {
        $request->ensureIsNotRateLimited();
        $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $exception) {
        // Assert that the exception contains the expected error message
        expect($exception->errors())
            ->toHaveKey('email')
            ->and($exception->errors()['email'][0])
            ->toContain('Too many login attempts');

        // Assert that the Lockout event was dispatched
        Event::assertDispatched(Lockout::class, function ($event) use ($request) {
            return $event->request === $request;
        });
    }
});

test('ensureIsNotRateLimited does not throw exception when not too many attempts', function () {
    $request = new LoginRequest();
    $request->merge(['email' => 'test@example.com']);
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    // Mock RateLimiter to simulate not too many attempts
    RateLimiter::shouldReceive('tooManyAttempts')
        ->once()
        ->with($request->throttleKey(), 5)
        ->andReturn(false);

    // This should not throw an exception
    $request->ensureIsNotRateLimited();

    // If we reach here, the test passes
    expect(true)->toBeTrue();
});

test('authenticate method throws exception for invalid credentials', function () {
    $request = new LoginRequest();
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    // Mock Auth facade
    \Illuminate\Support\Facades\Auth::shouldReceive('attempt')
        ->once()
        ->with([
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ], false)
        ->andReturn(false);

    // Mock RateLimiter
    RateLimiter::shouldReceive('tooManyAttempts')->andReturn(false);
    RateLimiter::shouldReceive('hit')
        ->once()
        ->with($request->throttleKey());

    try {
        $request->authenticate();
        $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $exception) {
        expect($exception->errors())
            ->toHaveKey('email')
            ->and($exception->errors()['email'][0])
            ->toContain('credentials do not match');
    }
});

test('authenticate method clears rate limiter for valid credentials', function () {
    $request = new LoginRequest();
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'correct-password',
    ]);

    // Mock Auth facade
    \Illuminate\Support\Facades\Auth::shouldReceive('attempt')
        ->once()
        ->with([
            'email' => 'test@example.com',
            'password' => 'correct-password',
        ], false)
        ->andReturn(true);

    // Mock RateLimiter
    RateLimiter::shouldReceive('tooManyAttempts')->andReturn(false);
    RateLimiter::shouldReceive('clear')
        ->once()
        ->with($request->throttleKey());

    $request->authenticate();

    // If we reach here without exceptions, the test passes
    expect(true)->toBeTrue();
});
