<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Collection;

class StockService
{
    public function deductForInvoice(Invoice $invoice): void
    {
        $invoice->loadMissing('items.product');

        foreach ($invoice->items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->adjust($item->product, -1 * (float) $item->quantity, 'sale', "Invoice {$invoice->invoice_number}", $invoice->user, 'invoice', $invoice->id);
        }
    }

    public function restoreForInvoice(Invoice $invoice): void
    {
        $invoice->loadMissing('items.product');

        foreach ($invoice->items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->adjust($item->product, (float) $item->quantity, 'adjustment', "Cancel {$invoice->invoice_number}", $invoice->user, 'invoice', $invoice->id);
        }
    }

    public function adjust(Product $product, float $quantity, string $type, string $notes, ?User $user = null, ?string $referenceType = null, ?int $referenceId = null): StockMovement
    {
        $before = (float) $product->stock_quantity;
        $after = $before + $quantity;

        $product->forceFill(['stock_quantity' => $after])->save();

        return StockMovement::query()->create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
        ]);
    }

    public function getMovementsForProduct(Product $product): Collection
    {
        return $product->stockMovements()->latest()->get();
    }
}
