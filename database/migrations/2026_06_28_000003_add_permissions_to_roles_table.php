<?php

use App\Enums\UserRole;
use App\Support\PermissionCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->json('permissions')->nullable()->after('description');
            $table->boolean('is_system')->default(true)->after('permissions');
        });

        foreach (UserRole::cases() as $role) {
            DB::table('roles')
                ->where('name', $role->value)
                ->update([
                    'permissions' => json_encode(PermissionCatalog::expand($role->permissions())),
                    'is_system' => true,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropColumn(['permissions', 'is_system']);
        });
    }
};
