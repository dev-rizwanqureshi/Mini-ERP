<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'unit_price' => $this->unit_price,
            'stock_quantity' => $this->stock_quantity,
            'is_low_stock' => $this->isLowStock(),
            'status' => $this->status?->value,
        ];
    }
}
