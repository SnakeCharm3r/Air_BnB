<?php

namespace App\Enums;

enum CustomerType: string
{
    case WalkIn = 'walk_in';
    case Corporate = 'corporate';
    case Government = 'government';
    case NGO = 'ngo';
    case VIP = 'vip';

    public function label(): string
    {
        return match ($this) {
            self::WalkIn => 'Walk In',
            self::Corporate => 'Corporate',
            self::Government => 'Government',
            self::NGO => 'NGO',
            self::VIP => 'VIP',
        };
    }
}
