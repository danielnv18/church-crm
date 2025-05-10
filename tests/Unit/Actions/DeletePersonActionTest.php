<?php

declare(strict_types=1);

use App\Actions\DeletePersonAction;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes a person', function (): void {
    // Arrange
    $person = Person::factory()->create();
    $action = new DeletePersonAction();

    // Act
    $action->handle($person);

    // Assert
    $this->assertSoftDeleted('people', [
        'id' => $person->id,
    ]);
});
