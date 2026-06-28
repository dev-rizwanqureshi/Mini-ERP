<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-'.now()->year.'-'.fake()->unique()->numerify('#####'),
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
            'invoice_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'due_date' => fake()->dateTimeBetween('now', '+45 days'),
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'status' => InvoiceStatus::Draft,
        ];
    }
}
