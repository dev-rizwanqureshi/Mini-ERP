<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksRolePermissions;

class ReportPolicy
{
    use ChecksRolePermissions;

    public function viewAny(User $user): bool { return $this->allows($user, 'reports', 'viewAny') || $this->allows($user, 'reports', 'view'); }
    public function view(User $user): bool { return $this->allows($user, 'reports', 'view'); }
    public function export(User $user): bool { return $this->allows($user, 'reports', 'export'); }
}
