<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ActionEnum;
use App\Enums\RoleEnum;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class UserSeeder extends Seeder
{
    private array $models = [
        User::class,
        Person::class,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->assignAdminPermissions();
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
            $modelName = Str::snake(class_basename($model));

            // Generate permissions for each action and model
            $permissions = [];
            foreach (ActionEnum::cases() as $action) {
                $permissions[] = "{$action->value} {$modelName}";
            }

            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
            }
        }
    }

    private function assignPermissionsToRoles(): void
    {
        // Only admins can manage users model
        $adminRole = Role::findByName(RoleEnum::Admin->value);
        foreach (ActionEnum::userPermissions() as $action) {
            $permission = "{$action} user";
            if (Permission::where('name', $permission)->exists()) {
                $adminRole->givePermissionTo($permission);
            }
        }

        // Admin and Pastors can manage person model
        $pastorRole = Role::findByName(RoleEnum::Pastor->value);
        foreach (ActionEnum::userPermissions() as $action) {
            $permission = "{$action} person";
            if (Permission::where('name', $permission)->exists()) {
                $adminRole->givePermissionTo($permission);
                $pastorRole->givePermissionTo($permission);
            }
        }
    }

    private function assignAdminPermissions(): void
    {
        // Only admins can restore and force delete any model
        $adminRole = Role::findByName(RoleEnum::Admin->value);
        foreach ($this->models as $model) {
            $modelName = Str::snake(class_basename($model));
            foreach (ActionEnum::adminPermissions() as $action) {
                if (in_array($action, ['restore', 'force delete'])) {
                    $permission = "{$action} {$modelName}";
                    $adminRole->givePermissionTo($permission);
                }
            }
        }
    }
}
