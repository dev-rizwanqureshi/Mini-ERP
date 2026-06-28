<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 15)
    {
        return Customer::query()->withCount('invoices')->search($filters['search'] ?? null)->latest()->paginate($perPage)->withQueryString();
    }
}
