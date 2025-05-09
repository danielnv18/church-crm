<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        // Fetch all persons from the database
        $people = Person::all();

        // Return a view with the persons data
        return Inertia::render('people/index', [
            'people' => $people,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return a view for creating a new person
        return Inertia::render('people/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request): RedirectResponse
    {
        // Validate and create a new person
        $validatedData = $request->validated();
        Person::create($validatedData);

        // Redirect to the index page with a success message
        return to_route(('people.index'))->with('success', 'Person created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person): Response
    {
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
        // Return a view for editing the person's details
        return Inertia::render('people/edit', [
            'person' => $person,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person): RedirectResponse
    {
        // Validate and update the person's details
        $validatedData = $request->validated();
        $person->update($validatedData);

        // Redirect to the index page with a success message
        return to_route('people.index')->with('success', 'Person updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person): RedirectResponse
    {
        // Delete the person from the database
        $person->delete();

        // Redirect to the index page with a success message
        return to_route('people.index')->with('success', 'Person deleted successfully.');
    }
}
