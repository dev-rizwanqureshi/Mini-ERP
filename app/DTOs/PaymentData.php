<?php

namespace App\DTOs;

use App\Enums\PaymentMethod;
use App\Http\Requests\Payment\StorePaymentRequest;

readonly class PaymentData
{
    public function __construct(
        public int $invoiceId,
        public int $customerId,
        public string $paymentDate,
        public float $amount,
        public PaymentMethod $paymentMethod,
        public ?string $referenceNumber,
        public ?string $notes,
    ) {}

    public static function fromRequest(StorePaymentRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            invoiceId: (int) $validated['invoice_id'],
            customerId: (int) $validated['customer_id'],
            paymentDate: (string) $validated['payment_date'],
            amount: (float) $validated['amount'],
            paymentMethod: PaymentMethod::from($validated['payment_method']),
            referenceNumber: $validated['reference_number'] ?? null,
            notes: $validated['notes'] ?? null,
        );
    }
}
