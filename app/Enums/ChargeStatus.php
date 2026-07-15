<?php

namespace App\Enums;

enum ChargeStatus: string
{
    case Posted = 'posted';
    case Reversed = 'reversed';
    case Void = 'void';

    public function label(): string
    {
        return match ($this) {
            self::Posted => 'Posted',
            self::Reversed => 'Reversed',
            self::Void => 'Void',
        };
    }
}
