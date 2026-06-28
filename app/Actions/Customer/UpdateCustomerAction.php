<?php

namespace App\Actions\Customer;

use App\DTOs\CustomerData;
use App\Models\Customer;

class UpdateCustomerAction
{
    public function execute(Customer $customer, CustomerData $data): Customer
    {
        $customer->update($data->toArray());

        return $customer;
    }
}
