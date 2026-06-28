<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company_name' => fake()->company(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode(),
            'tax_number' => strtoupper(fake()->bothify('VAT#######')),
            'credit_limit' => fake()->randomFloat(2, 0, 10000),
            'status' => fake()->randomElement(CustomerStatus::cases())->value,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
