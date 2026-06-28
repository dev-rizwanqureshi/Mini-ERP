<?php

namespace App\Listeners;

use App\Jobs\SendInvoiceEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvoiceEmailListener implements ShouldQueue
{
    public function handle(object $event): void
    {
        if (isset($event->invoice)) {
            SendInvoiceEmailJob::dispatch($event->invoice);
        }
    }
}
