<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Super Admin', 'superadmin@erp.test', UserRole::SuperAdmin],
            ['Admin', 'admin@erp.test', UserRole::Admin],
            ['Accountant', 'accountant@erp.test', UserRole::Accountant],
            ['Sales', 'sales@erp.test', UserRole::SalesUser],
            ['Viewer', 'viewer@erp.test', UserRole::Viewer],
        ];

        foreach ($users as [$name, $email, $role]) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role_id' => Role::query()->where('name', $role->value)->value('id'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }
    }
}
