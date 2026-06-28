<?php

namespace App\Actions\Invoice;

use App\Services\InvoiceService;

class GenerateInvoiceNumberAction
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function execute(): string
    {
        return $this->invoiceService->generateInvoiceNumber();
    }
}
