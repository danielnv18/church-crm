<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreatePersonAction;
use App\Actions\DeletePersonAction;
use App\Actions\UpdatePersonAction;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Person::class);

        // Fetch all people from the database
        $people = Person::query()
            ->with(['contactInformation', 'spiritualInformation'])
            ->orderBy('first_name', 'desc')
            ->get();

        // Return a view with the people data
        return Inertia::render('people/index', [
            'people' => $people,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('create', Person::class);

        // Return a view for creating a new person
        return Inertia::render('people/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request, CreatePersonAction $action): RedirectResponse
    {
        Gate::authorize('create', Person::class);

        $action->handle($request->validated());

        // Redirect to the index page with a success message
        return to_route(('people.index'))->with('success', 'Person created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person): Response
    {
        Gate::authorize('view', $person);

        // Return a view with the person's details
        return Inertia::render('people/show', [
            'person' => $person,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person): Response
    {
        Gate::authorize('update', $person);

        // Return a view for editing the person's details
        return Inertia::render('people/edit', [
            'person' => $person,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person, UpdatePersonAction $action): RedirectResponse
    {
        Gate::authorize('update', $person);

        $action->handle($person, $request->validated());

        // Redirect to the index page with a success message
        return to_route('people.index')->with('success', 'Person updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person, DeletePersonAction $action): RedirectResponse
    {
        Gate::authorize('delete', $person);

        // Delete the person using the action
        $action->handle($person);

        // Redirect to the index page with a success message
        return to_route('people.index')->with('success', 'Person deleted successfully.');
    }
}
