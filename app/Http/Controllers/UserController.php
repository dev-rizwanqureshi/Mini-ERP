<?php

namespace App\Http\Controllers;

use App\Enums\UserGender;
use App\Models\Role;
use App\Models\User;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->with('role')
            ->when($request->string('search')->toString(), fn ($query, string $term) => $query->where(function ($query) use ($term): void {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhereHas('role', fn ($query) => $query->where('display_name', 'like', "%{$term}%"));
            }));

        TableQuery::applySort($query, $request, [
            'name' => 'name',
            'email' => 'email',
            'gender' => 'gender',
            'is_active' => 'is_active',
            'created_at' => 'created_at',
            'role' => fn ($query, string $direction) => $query->orderBy(Role::query()->select('display_name')->whereColumn('roles.id', 'users.role_id'), $direction),
        ]);

        return Inertia::render('Users/Index', [
            'users' => $query->paginate(15)->withQueryString(),
            'filters' => TableQuery::filters($request, ['search', 'sort', 'direction']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create', [
            'roles' => $this->assignableRoles(request()->user()),
            'genders' => $this->genderOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'gender' => ['nullable', Rule::enum(UserGender::class)],
            'is_active' => ['boolean'],
        ]);
        $this->ensureAssignableRole($request, $data['role_id'] ?? null);
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = (bool) ($data['is_active'] ?? filled($data['role_id'] ?? null));
        User::query()->create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('Users/Edit', [
            'managedUser' => $user->load('role'),
            'roles' => $this->assignableRoles(request()->user()),
            'genders' => $this->genderOptions(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'gender' => ['nullable', Rule::enum(UserGender::class)],
            'is_active' => ['boolean'],
        ]);
        $this->ensureAssignableRole($request, $data['role_id'] ?? null);
        $this->ensureSuperAdminContinuity($user, $data);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    private function genderOptions(): array
    {
        return collect(UserGender::cases())
            ->map(fn (UserGender $gender) => ['value' => $gender->value, 'label' => $gender->label()])
            ->all();
    }

    private function assignableRoles(User $actor)
    {
        return Role::query()
            ->when(! $actor->isSuperAdmin(), fn ($query) => $query->where('name', '!=', 'super_admin'))
            ->orderBy('display_name')
            ->get();
    }

    private function ensureAssignableRole(Request $request, mixed $roleId): void
    {
        if (! $roleId) {
            return;
        }

        $role = Role::query()->findOrFail($roleId);
        abort_if($role->name === 'super_admin' && ! $request->user()?->isSuperAdmin(), 403, 'Only a super admin can assign the Super Admin role.');
    }

    private function ensureSuperAdminContinuity(User $user, array $data): void
    {
        if (! $user->hasRole('super_admin')) {
            return;
        }

        $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');
        $wouldRemainSuperAdmin = (int) ($data['role_id'] ?? 0) === (int) $superAdminRoleId && (bool) ($data['is_active'] ?? false);

        if ($wouldRemainSuperAdmin) {
            return;
        }

        $otherActiveSuperAdmins = User::query()
            ->whereKeyNot($user->id)
            ->where('role_id', $superAdminRoleId)
            ->where('is_active', true)
            ->exists();

        abort_unless($otherActiveSuperAdmins, 422, 'At least one active Super Admin must remain.');
    }
}
