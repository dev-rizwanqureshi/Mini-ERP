<?php

namespace App\Actions\Invoice;

use App\DTOs\InvoiceData;
use App\Models\User;
use App\Services\InvoiceService;

class CreateInvoiceAction
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function execute(InvoiceData $data, User $creator)
    {
        return $this->invoiceService->create($data, $creator);
    }
}
