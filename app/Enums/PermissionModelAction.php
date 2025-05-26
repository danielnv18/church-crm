<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionModelAction: string
{
    case ViewAny = 'view any';
    case View = 'view';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case Restore = 'restore';
    case ForceDelete = 'force delete';

    // Get only admin permissions
    /**
     * @return array<string>
     */
    public static function adminPermissions(): array
    {
        return [
            self::Restore->value,
            self::ForceDelete->value,
        ];
    }

    // Get only user permissions
    /**
     * @return array<string>
     */
    public static function userPermissions(): array
    {
        return [
            self::ViewAny->value,
            self::View->value,
            self::Create->value,
            self::Update->value,
            self::Delete->value,
        ];
    }
}
