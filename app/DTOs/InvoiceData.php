<?php

namespace App\DTOs;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;

readonly class InvoiceData
{
    public function __construct(
        public int $customerId,
        public string $invoiceDate,
        public string $dueDate,
        public string $discountType,
        public float $discountValue,
        public ?string $notes,
        public ?string $terms,
        public ?string $footer,
        public string $currencyCode,
        public string $currencySymbol,
        public array $items,
    ) {}

    public static function fromRequest(StoreInvoiceRequest|UpdateInvoiceRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            customerId: (int) $validated['customer_id'],
            invoiceDate: (string) $validated['invoice_date'],
            dueDate: (string) $validated['due_date'],
            discountType: (string) ($validated['discount_type'] ?? 'fixed'),
            discountValue: (float) ($validated['discount_value'] ?? 0),
            notes: $validated['notes'] ?? null,
            terms: $validated['terms'] ?? null,
            footer: $validated['footer'] ?? null,
            currencyCode: (string) ($validated['currency_code'] ?? 'USD'),
            currencySymbol: (string) ($validated['currency_symbol'] ?? '$'),
            items: collect($validated['items'])->values()->map(fn (array $item, int $index) => InvoiceItemData::fromArray($item, $index))->all(),
        );
    }
}
