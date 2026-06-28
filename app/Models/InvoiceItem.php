<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invoice_id', 'product_id', 'description', 'quantity', 'unit_price', 'tax_rate', 'tax_amount', 'discount_type', 'discount_value', 'discount_amount', 'total', 'sort_order'])]
class InvoiceItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotals(): void
    {
        $base = (float) $this->quantity * (float) $this->unit_price;
        $tax = round($base * ((float) $this->tax_rate / 100), 2);
        $discount = $this->discount_type === 'percentage'
            ? round($base * ((float) $this->discount_value / 100), 2)
            : round((float) $this->discount_value, 2);

        $this->tax_amount = $tax;
        $this->discount_amount = $discount;
        $this->total = max(0, round($base + $tax - $discount, 2));
    }
}
