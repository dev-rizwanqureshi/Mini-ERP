<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Paid = 'paid';
    case Partial = 'partial';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Paid => 'success',
            self::Sent, self::Partial => 'info',
            self::Overdue => 'danger',
            self::Cancelled => 'neutral',
            self::Draft => 'warning',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::Draft, self::Sent], true);
    }

    public function canDelete(): bool
    {
        return $this === self::Draft;
    }

    public function canSend(): bool
    {
        return in_array($this, [self::Draft, self::Sent], true);
    }

    public function canReceivePayment(): bool
    {
        return in_array($this, [self::Sent, self::Partial, self::Overdue], true);
    }
}
