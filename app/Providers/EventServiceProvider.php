<?php

namespace App\Providers;

use App\Events\InvoiceCreated;
use App\Events\InvoicePaid;
use App\Events\InvoiceStatusChanged;
use App\Events\PaymentReceived;
use App\Listeners\CreateActivityLogListener;
use App\Listeners\NotifyAdminOnPaymentListener;
use App\Listeners\UpdateInvoiceStatusListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoiceCreated::class => [CreateActivityLogListener::class],
        InvoiceStatusChanged::class => [CreateActivityLogListener::class],
        InvoicePaid::class => [CreateActivityLogListener::class, NotifyAdminOnPaymentListener::class],
        PaymentReceived::class => [UpdateInvoiceStatusListener::class, CreateActivityLogListener::class],
    ];
}
