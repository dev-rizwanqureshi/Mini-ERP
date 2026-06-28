<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserGender;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Super Admin', 'superadmin@erp.test', UserRole::SuperAdmin, UserGender::PreferNotToSay],
            ['Admin', 'admin@erp.test', UserRole::Admin, UserGender::PreferNotToSay],
            ['Accountant', 'accountant@erp.test', UserRole::Accountant, UserGender::PreferNotToSay],
            ['Sales', 'sales@erp.test', UserRole::SalesUser, UserGender::PreferNotToSay],
            ['Viewer', 'viewer@erp.test', UserRole::Viewer, UserGender::PreferNotToSay],
        ];

        foreach ($users as [$name, $email, $role, $gender]) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role_id' => Role::query()->where('name', $role->value)->value('id'),
                    'gender' => $gender->value,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }
    }
}
