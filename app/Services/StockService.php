<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

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
        return DB::transaction(function () use ($product, $quantity, $type, $notes, $user, $referenceType, $referenceId): StockMovement {
            $product->refresh();
            $before = (float) $product->stock_quantity;
            $after = $before + $quantity;

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stock cannot go below zero.',
                ]);
            }

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
        });
    }

    public function receive(Product $product, float $quantity, ?User $user = null, ?string $notes = null): StockMovement
    {
        return $this->adjust($product, abs($quantity), 'purchase', $notes ?: 'Stock received', $user);
    }

    public function remove(Product $product, float $quantity, ?User $user = null, ?string $notes = null): StockMovement
    {
        return $this->adjust($product, -1 * abs($quantity), 'adjustment', $notes ?: 'Stock removed', $user);
    }

    public function correctTo(Product $product, float $targetQuantity, ?User $user = null, ?string $notes = null): ?StockMovement
    {
        $difference = $targetQuantity - (float) $product->stock_quantity;

        if (abs($difference) < 0.01) {
            return null;
        }

        return $this->adjust($product, $difference, 'correction', $notes ?: 'Stock corrected', $user);
    }

    public function getMovementsForProduct(Product $product): Collection
    {
        return $product->stockMovements()->latest()->get();
    }
}
