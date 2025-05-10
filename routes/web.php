<?php

declare(strict_types=1);

use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Person routes
    Route::get('people', [PersonController::class, 'index'])->name('people.index');
    Route::get('people/create', [PersonController::class, 'create'])->name('people.create');
    Route::post('people', [PersonController::class, 'store'])->name('people.store');
    Route::get('people/{person}', [PersonController::class, 'show'])->name('people.show');
    Route::get('people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
    Route::put('people/{person}', [PersonController::class, 'update'])->name('people.update');
    Route::delete('people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
