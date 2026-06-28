<?php

namespace App\Events;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(public Invoice $invoice, public InvoiceStatus $previousStatus) {}
}
