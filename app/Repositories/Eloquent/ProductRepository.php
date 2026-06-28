<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 15)
    {
        return Product::query()->search($filters['search'] ?? null)->latest()->paginate($perPage)->withQueryString();
    }
}
