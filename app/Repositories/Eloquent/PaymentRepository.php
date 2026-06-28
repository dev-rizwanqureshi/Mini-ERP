<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 15)
    {
        return Payment::query()->with(['invoice', 'customer', 'user'])->latest()->paginate($perPage)->withQueryString();
    }
}
