<?php

namespace App\DTOs;

use App\Enums\CustomerStatus;

readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $companyName,
        public ?string $address,
        public ?string $city,
        public ?string $state,
        public ?string $country,
        public ?string $postalCode,
        public ?string $taxNumber,
        public float $creditLimit,
        public CustomerStatus $status,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            email: (string) $data['email'],
            phone: $data['phone'] ?? null,
            companyName: $data['company_name'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            country: $data['country'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            taxNumber: $data['tax_number'] ?? null,
            creditLimit: (float) ($data['credit_limit'] ?? 0),
            status: CustomerStatus::from($data['status'] ?? CustomerStatus::Active->value),
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_name' => $this->companyName,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'tax_number' => $this->taxNumber,
            'credit_limit' => $this->creditLimit,
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }
}
