<?php

namespace App\Services;

use App\DTOs\CustomerData;
use App\Models\Customer;
use App\Models\User;

class CustomerService
{
    public function create(CustomerData $data, User $creator): Customer
    {
        return Customer::query()->create([...$data->toArray(), 'created_by' => $creator->id]);
    }

    public function update(Customer $customer, CustomerData $data): Customer
    {
        $customer->update($data->toArray());

        return $customer;
    }
}
