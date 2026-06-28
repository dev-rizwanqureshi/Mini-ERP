<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksRolePermissions
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    protected function allows(User $user, string $resource, string $ability): bool
    {
        return $user->canDo("{$resource}.{$ability}");
    }
}
