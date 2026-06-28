<?php

namespace App\DTOs;

readonly class InvoiceItemData
{
    public function __construct(
        public ?int $productId,
        public string $description,
        public float $quantity,
        public float $unitPrice,
        public float $taxRate,
        public string $discountType = 'fixed',
        public float $discountValue = 0,
        public int $sortOrder = 0,
    ) {}

    public static function fromArray(array $item, int $sortOrder = 0): self
    {
        return new self(
            productId: isset($item['product_id']) ? (int) $item['product_id'] : null,
            description: (string) $item['description'],
            quantity: (float) $item['quantity'],
            unitPrice: (float) $item['unit_price'],
            taxRate: (float) ($item['tax_rate'] ?? 0),
            discountType: (string) ($item['discount_type'] ?? 'fixed'),
            discountValue: (float) ($item['discount_value'] ?? 0),
            sortOrder: $sortOrder,
        );
    }
}
