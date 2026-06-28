<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\InvoiceService;

class UpdateInvoiceStatusListener
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function handle(PaymentReceived $event): void
    {
        $this->invoiceService->updateStatusAfterPayment($event->payment->invoice);
    }
}
