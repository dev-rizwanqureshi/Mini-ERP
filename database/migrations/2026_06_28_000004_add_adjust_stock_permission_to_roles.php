<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->orderBy('id')->each(function (object $role): void {
            $permissions = json_decode((string) $role->permissions, true) ?: [];

            if (! in_array('products.adjustStock', $permissions, true) && $this->shouldReceiveStockPermission($permissions)) {
                $permissions[] = 'products.adjustStock';

                DB::table('roles')->where('id', $role->id)->update([
                    'permissions' => json_encode(array_values(array_unique($permissions))),
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('roles')->orderBy('id')->each(function (object $role): void {
            $permissions = json_decode((string) $role->permissions, true) ?: [];
            $permissions = array_values(array_filter($permissions, fn (string $permission): bool => $permission !== 'products.adjustStock'));

            DB::table('roles')->where('id', $role->id)->update([
                'permissions' => json_encode($permissions),
            ]);
        });
    }

    private function shouldReceiveStockPermission(array $permissions): bool
    {
        return in_array('*', $permissions, true)
            || in_array('products.*', $permissions, true)
            || in_array('products.update', $permissions, true)
            || in_array('products.delete', $permissions, true);
    }
};
