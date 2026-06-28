<?php

namespace App\Services;

use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;
use App\Enums\InvoiceStatus;
use App\Events\InvoiceCreated;
use App\Events\InvoiceStatusChanged;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(private readonly StockService $stockService) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::query()
            ->with(['customer', 'items.product', 'payments', 'user'])
            ->when($filters['search'] ?? null, fn ($query, $term) => $query->where('invoice_number', 'like', "%{$term}%"))
            ->byStatus($filters['status'] ?? null)
            ->when($filters['customer_id'] ?? null, fn ($query, $customerId) => $query->forCustomer((int) $customerId))
            ->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(InvoiceData $data, User $creator): Invoice
    {
        return DB::transaction(function () use ($data, $creator): Invoice {
            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $data->customerId,
                'user_id' => $creator->id,
                'invoice_date' => $data->invoiceDate,
                'due_date' => $data->dueDate,
                'discount_type' => $data->discountType,
                'discount_value' => $data->discountValue,
                'currency_code' => $data->currencyCode,
                'currency_symbol' => $data->currencySymbol,
                'notes' => $data->notes,
                'terms' => $data->terms,
                'footer' => $data->footer,
                'status' => InvoiceStatus::Draft,
            ]);

            $this->syncItems($invoice, $data->items);
            $invoice->refresh()->recalculate();
            $this->stockService->deductForInvoice($invoice->refresh());
            event(new InvoiceCreated($invoice));

            return $invoice->load(['customer', 'items.product', 'payments']);
        });
    }

    public function update(Invoice $invoice, InvoiceData $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data): Invoice {
            $this->stockService->restoreForInvoice($invoice);
            $invoice->items()->delete();
            $invoice->update([
                'customer_id' => $data->customerId,
                'invoice_date' => $data->invoiceDate,
                'due_date' => $data->dueDate,
                'discount_type' => $data->discountType,
                'discount_value' => $data->discountValue,
                'currency_code' => $data->currencyCode,
                'currency_symbol' => $data->currencySymbol,
                'notes' => $data->notes,
                'terms' => $data->terms,
                'footer' => $data->footer,
            ]);
            $this->syncItems($invoice, $data->items);
            $invoice->refresh()->recalculate();
            $this->stockService->deductForInvoice($invoice->refresh());

            return $invoice->load(['customer', 'items.product', 'payments']);
        });
    }

    public function calculateTotals(array $items, float $discountValue, string $discountType, float $taxRate = 0): array
    {
        $subtotal = $tax = $lineDiscount = 0.0;

        foreach ($items as $item) {
            $quantity = (float) (is_array($item) ? $item['quantity'] : $item->quantity);
            $unitPrice = (float) (is_array($item) ? $item['unit_price'] : $item->unitPrice);
            $itemTaxRate = (float) (is_array($item) ? ($item['tax_rate'] ?? $taxRate) : $item->taxRate);
            $itemDiscountType = (string) (is_array($item) ? ($item['discount_type'] ?? 'fixed') : $item->discountType);
            $itemDiscountValue = (float) (is_array($item) ? ($item['discount_value'] ?? 0) : $item->discountValue);
            $base = $quantity * $unitPrice;

            $subtotal += $base;
            $tax += $base * ($itemTaxRate / 100);
            $lineDiscount += $itemDiscountType === 'percentage' ? $base * ($itemDiscountValue / 100) : $itemDiscountValue;
        }

        $invoiceDiscount = $discountType === 'percentage' ? $subtotal * ($discountValue / 100) : $discountValue;
        $total = max(0, $subtotal + $tax - $lineDiscount - $invoiceDiscount);

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($tax, 2),
            'discount_amount' => round($lineDiscount + $invoiceDiscount, 2),
            'total' => round($total, 2),
            'balance_amount' => round($total, 2),
        ];
    }

    public function generateInvoiceNumber(): string
    {
        return DB::transaction(function (): string {
            $prefix = Setting::query()->where('key', 'invoice_prefix')->lockForUpdate()->value('value') ?: 'INV';
            $next = Setting::query()->where('key', 'invoice_next_number')->lockForUpdate()->firstOrCreate(
                ['key' => 'invoice_next_number'],
                ['value' => '1', 'group' => 'invoice']
            );
            $number = (int) $next->value;
            $next->update(['value' => (string) ($number + 1)]);

            return sprintf('%s-%s-%05d', $prefix, now()->year, $number);
        });
    }

    public function markAsSent(Invoice $invoice): Invoice
    {
        abort_if($invoice->total <= 0, 422, 'Zero amount invoices cannot be sent.');
        $previous = $invoice->status;
        $invoice->update(['status' => InvoiceStatus::Sent, 'sent_at' => now()]);
        event(new InvoiceStatusChanged($invoice, $previous));

        return $invoice;
    }

    public function cancel(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice): Invoice {
            $previous = $invoice->status;
            $invoice->update(['status' => InvoiceStatus::Cancelled]);
            $this->stockService->restoreForInvoice($invoice);
            event(new InvoiceStatusChanged($invoice, $previous));

            return $invoice;
        });
    }

    public function updateStatusAfterPayment(Invoice $invoice): void
    {
        $previous = $invoice->status;
        $invoice->recalculate();
        $invoice->update([
            'status' => (float) $invoice->balance_amount <= 0.0 ? InvoiceStatus::Paid : InvoiceStatus::Partial,
            'paid_at' => (float) $invoice->balance_amount <= 0.0 ? now() : null,
        ]);
        event(new InvoiceStatusChanged($invoice, $previous));
    }

    public function getOverdueInvoices(): Collection
    {
        return Invoice::query()->overdue()->get();
    }

    public function markOverdueInvoices(): void
    {
        $this->getOverdueInvoices()->each(function (Invoice $invoice): void {
            $previous = $invoice->status;
            $invoice->update(['status' => InvoiceStatus::Overdue]);
            event(new InvoiceStatusChanged($invoice, $previous));
        });
    }

    private function syncItems(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            if ($item instanceof InvoiceItemData) {
                $product = $item->productId ? Product::query()->find($item->productId) : null;
                $model = new InvoiceItem([
                    'product_id' => $item->productId,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unitPrice,
                    'tax_rate' => $item->taxRate,
                    'discount_type' => $item->discountType,
                    'discount_value' => $item->discountValue,
                    'sort_order' => $item->sortOrder,
                ]);
                $model->calculateTotals();
                $invoice->items()->save($model);

                abort_if($product && $product->stock_quantity < $item->quantity, 422, "Insufficient stock for {$product->name}.");
            }
        }
    }
}
