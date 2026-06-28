<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Policies\Concerns\ChecksRolePermissions;

class ProductPolicy
{
    use ChecksRolePermissions;

    public function viewAny(User $user): bool { return $this->allows($user, 'products', 'viewAny'); }
    public function view(User $user, Product $product): bool { return $this->allows($user, 'products', 'view'); }
    public function create(User $user): bool { return $this->allows($user, 'products', 'create'); }
    public function update(User $user, Product $product): bool { return $this->allows($user, 'products', 'update'); }
    public function delete(User $user, Product $product): bool { return $this->allows($user, 'products', 'delete') && ! $product->invoiceItems()->exists(); }
}
