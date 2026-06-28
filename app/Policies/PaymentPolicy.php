<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use App\Policies\Concerns\ChecksRolePermissions;

class PaymentPolicy
{
    use ChecksRolePermissions;

    public function viewAny(User $user): bool { return $this->allows($user, 'payments', 'viewAny'); }
    public function view(User $user, Payment $payment): bool { return $this->allows($user, 'payments', 'view'); }
    public function create(User $user): bool { return $this->allows($user, 'payments', 'create'); }
    public function delete(User $user, Payment $payment): bool { return $this->allows($user, 'payments', 'delete'); }
}
