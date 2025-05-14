<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateUserAction;
use App\Actions\DeleteUserAction;
use App\Actions\UpdateUserAction;
use App\Enums\ActionEnum;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

final class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        // Check if the user has the necessary permissions
        if (! auth()->user()->can(ActionEnum::ViewAny->value.' user')) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch all users from the database
        $users = User::with('roles:id,name')->get();

        // Return a view with the users data
        return Inertia::render('users/index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        // Check if the user has the necessary permissions
        if (! auth()->user()->can(ActionEnum::Create->value.' user')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();

        // Return a view for creating a new user
        return Inertia::render('users/create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        // Redirect to the index page with a success message
        return to_route(('users.index'))->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        $user->load('roles:id,name');

        // Return a view with the user's details
        return Inertia::render('users/show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        $roles = Role::all();

        // Return a view for editing the user's details
        return Inertia::render('users/edit', [
            'user' => $user->load('roles:id,name'),
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $action->handle($user, $request->validated());

        // Redirect to the index page with a success message
        return to_route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        $action->handle($user);

        // Redirect to the index page with a success message
        return to_route('users.index')->with('success', 'User deleted successfully.');
    }
}
