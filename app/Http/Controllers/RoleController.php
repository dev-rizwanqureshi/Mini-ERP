<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Support\PermissionCatalog;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $query = Role::query()
            ->withCount('users')
            ->when($request->string('search')->toString(), fn ($query, string $term) => $query->where(function ($query) use ($term): void {
                $query->where('display_name', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            }));

        TableQuery::applySort($query, $request, [
            'display_name' => 'display_name',
            'name' => 'name',
            'users_count' => 'users_count',
            'is_system' => 'is_system',
            'created_at' => 'created_at',
        ], 'display_name', 'asc');

        return Inertia::render('Roles/Index', [
            'roles' => $query->get(),
            'permissionModules' => PermissionCatalog::modules(),
            'filters' => TableQuery::filters($request, ['search', 'sort', 'direction']),
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return Inertia::render('Roles/Create', [
            'permissionModules' => PermissionCatalog::modules(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $validated = $this->validateRolePayload($request);
        $name = $this->roleNameFromDisplayName($validated['display_name']);

        if ($name === '' || Role::query()->where('name', $name)->exists()) {
            throw ValidationException::withMessages([
                'display_name' => 'A role with this name already exists.',
            ]);
        }

        Role::query()->create([
            'name' => $name,
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'permissions' => PermissionCatalog::sanitize($validated['permissions'] ?? []),
            'is_system' => false,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Request $request, Role $role): Response
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissionModules' => PermissionCatalog::modules(),
            'allPermissions' => PermissionCatalog::allPermissions(),
            'selectedPermissions' => $role->name === 'super_admin'
                ? PermissionCatalog::allPermissions()
                : PermissionCatalog::expand($role->permissions ?? []),
            'isLocked' => $role->name === 'super_admin',
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        abort_if($role->name === 'super_admin', 422, 'The Super Admin role is protected and always has full access.');

        $validated = $this->validateRolePayload($request);

        $role->update([
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'permissions' => PermissionCatalog::sanitize($validated['permissions'] ?? []),
        ]);

        return redirect()->route('roles.index')->with('success', 'Role permissions updated successfully.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        abort_if($role->is_system, 422, 'System roles cannot be deleted.');
        abort_if($role->users()->exists(), 422, 'Move users to another role before deleting this role.');

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    private function validateRolePayload(Request $request): array
    {
        return $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);
    }

    private function roleNameFromDisplayName(string $displayName): string
    {
        return Str::of($displayName)->slug('_')->lower()->trim('_')->toString();
    }
}
