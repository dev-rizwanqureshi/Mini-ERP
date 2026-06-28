<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admins_can_open_role_management(): void
    {
        $admin = User::factory()->for($this->role('admin', ['users.viewAny']))->create();

        $this->actingAs($admin)
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_super_admins_can_create_custom_roles_with_sanitized_permissions(): void
    {
        $superAdmin = User::factory()->for($this->role('super_admin', ['*']))->create();

        $this->actingAs($superAdmin)
            ->post(route('roles.store'), [
                'display_name' => 'Support Agent',
                'description' => 'Handles customer support.',
                'permissions' => ['customers.viewAny', 'customers.view', 'not.allowed'],
            ])
            ->assertRedirect(route('roles.index'));

        $role = Role::query()->where('name', 'support_agent')->firstOrFail();

        $this->assertFalse($role->is_system);
        $this->assertSame(['customers.viewAny', 'customers.view'], $role->permissions);
    }

    public function test_super_admin_role_cannot_be_modified_or_deleted(): void
    {
        $superAdminRole = $this->role('super_admin', ['*']);
        $superAdmin = User::factory()->for($superAdminRole)->create();

        $this->actingAs($superAdmin)
            ->put(route('roles.update', $superAdminRole), [
                'display_name' => 'Super Admin',
                'description' => 'No access',
                'permissions' => [],
            ])
            ->assertStatus(422);

        $this->actingAs($superAdmin)
            ->delete(route('roles.destroy', $superAdminRole))
            ->assertStatus(422);
    }

    public function test_custom_roles_with_users_cannot_be_deleted(): void
    {
        $superAdmin = User::factory()->for($this->role('super_admin', ['*']))->create();
        $customRole = $this->role('billing_agent', ['payments.viewAny'], false);
        User::factory()->for($customRole)->create();

        $this->actingAs($superAdmin)
            ->delete(route('roles.destroy', $customRole))
            ->assertStatus(422);

        $this->assertDatabaseHas('roles', ['name' => 'billing_agent']);
    }

    private function role(string $name, array $permissions, bool $isSystem = true): Role
    {
        return Role::query()->create([
            'name' => $name,
            'display_name' => str($name)->replace('_', ' ')->title()->toString(),
            'description' => "{$name} role",
            'permissions' => $permissions,
            'is_system' => $isSystem,
        ]);
    }
}
