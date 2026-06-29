<?php

namespace App\Http\Requests\Invoice;

use App\Models\Invoice;
use App\Models\Product;

class UpdateInvoiceRequest extends StoreInvoiceRequest
{
    protected function availableStockFor(Product $product): float
    {
        $available = parent::availableStockFor($product);
        $invoice = $this->route('invoice');

        if (! $invoice instanceof Invoice) {
            return $available;
        }

        $invoice->loadMissing('items');

        return $available + (float) $invoice->items
            ->where('product_id', $product->id)
            ->sum('quantity');
    }
}
