<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class UserSeeder extends Seeder
{
    private array $actions = [
        'view any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force delete'
    ];

    private array $models = [
        User::class,
        Person::class
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->assignPermissionsToRoles();
    }

    private function seedRoles(): void
    {
        // Create roles
        foreach (RoleEnum::cases() as $role) {
            Role::create(['name' => $role->value]);
        }
    }

    private function seedPermissions(): void
    {
        // Create permissions for each model
        foreach ($this->models as $model) {
            $modelName = strtolower(class_basename($model));

            // Generate permissions for each action and model
            $permissions = [];
            foreach ($this->actions as $action) {
                $permissions[] = "{$action} {$modelName}";
            }

            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
            }
        }
    }

    private function assignPermissionsToRoles(): void
    {
        // Only admins can restore and force delete any model
        $adminRole = Role::findByName(RoleEnum::Admin->value);
        foreach ($this->models as $model) {
            $modelName = strtolower(class_basename($model));
            foreach ($this->actions as $action) {
                if (in_array($action, ['restore', 'force delete'])) {
                    $permission = "{$action} {$modelName}";
                    $adminRole->givePermissionTo($permission);
                }
            }
        }
    }
}
