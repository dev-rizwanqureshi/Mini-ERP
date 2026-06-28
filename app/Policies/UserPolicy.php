<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksRolePermissions;

class UserPolicy
{
    use ChecksRolePermissions;

    public function viewAny(User $user): bool { return $this->allows($user, 'users', 'viewAny'); }
    public function view(User $user, User $model): bool { return $this->allows($user, 'users', 'view'); }
    public function create(User $user): bool { return $this->allows($user, 'users', 'create'); }
    public function update(User $user, User $model): bool
    {
        if ($model->hasRole('super_admin') && ! $user->isSuperAdmin()) {
            return false;
        }

        return $this->allows($user, 'users', 'update');
    }
    public function delete(User $user, User $model): bool { return false; }
}
