<?php

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 15)
    {
        return Invoice::query()->with(['customer', 'items.product', 'payments'])->latest()->paginate($perPage)->withQueryString();
    }
}
