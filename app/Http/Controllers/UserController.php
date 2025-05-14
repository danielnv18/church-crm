<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateUserAction;
use App\Actions\DeleteUserAction;
use App\Actions\UpdateUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

final class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        // check the viewAny method in the UserPolicy
        if (! auth()->user()->can('viewAny', User::class)) {
            abort(ResponseCode::HTTP_FORBIDDEN, 'Unauthorized action.');
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
        // check the create method in the UserPolicy
        if (! auth()->user()->can('create', User::class)) {
            abort(ResponseCode::HTTP_FORBIDDEN, 'Unauthorized action.');
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
        // check the view method in the UserPolicy
        if (! auth()->user()->can('view', $user)) {
            abort(ResponseCode::HTTP_FORBIDDEN, 'Unauthorized action.');
        }

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
        // check the update method in the UserPolicy
        if (! auth()->user()->can('update', $user)) {
            abort(ResponseCode::HTTP_FORBIDDEN, 'Unauthorized action.');
        }

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
        // check the delete method in the UserPolicy
        if (! auth()->user()->can('delete', $user)) {
            abort(ResponseCode::HTTP_FORBIDDEN, 'Unauthorized action.');
        }

        $action->handle($user);

        // Redirect to the index page with a success message
        return to_route('users.index')->with('success', 'User deleted successfully.');
    }
}
