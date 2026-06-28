<?php

namespace App\Actions\Customer;

use App\DTOs\CustomerData;
use App\Models\Customer;
use App\Models\User;

class CreateCustomerAction
{
    public function execute(CustomerData $data, User $creator): Customer
    {
        return Customer::query()->create([...$data->toArray(), 'created_by' => $creator->id]);
    }
}
