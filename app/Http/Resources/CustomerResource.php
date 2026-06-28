<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'status' => $this->status?->value,
            'invoices_count' => $this->invoices_count ?? $this->invoices()->count(),
            'outstanding_balance' => $this->outstanding_balance,
        ];
    }
}
