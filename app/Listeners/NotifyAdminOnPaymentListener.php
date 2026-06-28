<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminOnPaymentListener implements ShouldQueue
{
    public function handle(object $event): void
    {
        logger()->info('Payment notification queued', ['event' => class_basename($event)]);
    }
}
