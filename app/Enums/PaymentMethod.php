<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Cheque = 'cheque';
    case CreditCard = 'credit_card';
    case Online = 'online';
    case Other = 'other';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash => 'banknote',
            self::BankTransfer => 'landmark',
            self::Cheque => 'file-text',
            self::CreditCard => 'credit-card',
            self::Online => 'globe',
            self::Other => 'circle-dollar-sign',
        };
    }
}
