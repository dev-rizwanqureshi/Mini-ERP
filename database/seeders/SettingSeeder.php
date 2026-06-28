<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'company_name' => ['Acme Mini ERP', 'general'],
            'company_email' => ['hello@erp.test', 'general'],
            'company_phone' => ['+1 555 0100', 'general'],
            'company_address' => ['100 Market Street', 'general'],
            'company_city' => ['San Francisco', 'general'],
            'company_country' => ['USA', 'general'],
            'company_logo' => [null, 'general'],
            'invoice_prefix' => ['INV', 'invoice'],
            'invoice_next_number' => ['1', 'invoice'],
            'currency_symbol' => ['$', 'invoice'],
            'currency_code' => ['USD', 'invoice'],
            'default_tax_rate' => ['0', 'tax'],
            'default_payment_terms' => ['30', 'invoice'],
            'invoice_footer_note' => ['Thank you for your business.', 'invoice'],
            'invoice_bank_details' => ['Bank: Demo Bank / Account: 123456', 'invoice'],
        ];

        foreach ($settings as $key => [$value, $group]) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }
    }
}
