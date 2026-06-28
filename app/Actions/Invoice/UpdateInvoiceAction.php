<?php

namespace App\Actions\Invoice;

use App\DTOs\InvoiceData;
use App\Models\Invoice;
use App\Services\InvoiceService;

class UpdateInvoiceAction
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function execute(Invoice $invoice, InvoiceData $data): Invoice
    {
        return $this->invoiceService->update($invoice, $data);
    }
}
