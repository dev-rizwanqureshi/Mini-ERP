<?php

namespace App\Jobs;

use App\Services\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateOverdueInvoicesJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('default');
    }

    public function handle(InvoiceService $invoiceService): void
    {
        $invoiceService->markOverdueInvoices();
    }
}
