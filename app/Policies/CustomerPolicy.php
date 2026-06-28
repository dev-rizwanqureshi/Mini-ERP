<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use App\Policies\Concerns\ChecksRolePermissions;

class CustomerPolicy
{
    use ChecksRolePermissions;

    public function viewAny(User $user): bool { return $this->allows($user, 'customers', 'viewAny'); }
    public function view(User $user, Customer $customer): bool { return $this->allows($user, 'customers', 'view'); }
    public function create(User $user): bool { return $this->allows($user, 'customers', 'create'); }
    public function update(User $user, Customer $customer): bool { return $this->allows($user, 'customers', 'update'); }
    public function delete(User $user, Customer $customer): bool { return $this->allows($user, 'customers', 'delete') && ! $customer->invoices()->exists(); }
}
