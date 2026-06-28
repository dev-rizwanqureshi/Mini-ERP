<?php

namespace App\Actions\Payment;

use App\DTOs\PaymentData;
use App\Models\User;
use App\Services\PaymentService;

class RecordPaymentAction
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function execute(PaymentData $data, User $recorder)
    {
        return $this->paymentService->record($data, $recorder);
    }
}
