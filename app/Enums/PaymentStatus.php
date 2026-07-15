<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case DepositPaid = 'deposit_paid';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Refunded = 'refunded';
    case Overpaid = 'overpaid';
    case Outstanding = 'outstanding';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Unpaid',
            self::DepositPaid => 'Deposit Paid',
            self::PartiallyPaid => 'Partially Paid',
            self::Paid => 'Paid',
            self::Refunded => 'Refunded',
            self::Overpaid => 'Overpaid',
            self::Outstanding => 'Outstanding',
        };
    }
}
