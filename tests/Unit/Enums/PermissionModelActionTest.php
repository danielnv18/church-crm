<?php

declare(strict_types=1);

use App\Enums\PermissionModelAction;
use Tests\TestCase;

uses(TestCase::class);

it('has the correct cases with values', function (): void {
    expect(PermissionModelAction::ViewAny->value)->toBe('view any')
        ->and(PermissionModelAction::View->value)->toBe('view')
        ->and(PermissionModelAction::Create->value)->toBe('create')
        ->and(PermissionModelAction::Update->value)->toBe('update')
        ->and(PermissionModelAction::Delete->value)->toBe('delete')
        ->and(PermissionModelAction::Restore->value)->toBe('restore')
        ->and(PermissionModelAction::ForceDelete->value)->toBe('force delete');
});

it('returns the correct admin permissions', function (): void {
    $adminPermissions = PermissionModelAction::adminPermissions();

    expect($adminPermissions)->toBe([
        'restore',
        'force delete',
    ]);
});

it('returns the correct user permissions', function (): void {
    $userPermissions = PermissionModelAction::userPermissions();

    expect($userPermissions)->toBe([
        'view any',
        'view',
        'create',
        'update',
        'delete',
    ]);
});
