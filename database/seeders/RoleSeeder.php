<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (UserRole::cases() as $role) {
            Role::query()->updateOrCreate(
                ['name' => $role->value],
                [
                    'display_name' => $role->label(),
                    'description' => "{$role->label()} role",
                    'permissions' => PermissionCatalog::expand($role->permissions()),
                    'is_system' => true,
                ]
            );
        }
    }
}
