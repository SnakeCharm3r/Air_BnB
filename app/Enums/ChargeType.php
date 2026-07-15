<?php

namespace App\Enums;

enum ChargeType: string
{
    case Room = 'room';
    case Restaurant = 'restaurant';
    case Laundry = 'laundry';
    case MiniBar = 'mini_bar';
    case RoomService = 'room_service';
    case Spa = 'spa';
    case Transport = 'transport';
    case Damage = 'damage';
    case Conference = 'conference';
    case EquipmentHire = 'equipment_hire';
    case ExtraBed = 'extra_bed';
    case EarlyCheckIn = 'early_check_in';
    case LateCheckOut = 'late_check_out';
    case Miscellaneous = 'miscellaneous';

    public function label(): string
    {
        return match ($this) {
            self::Room => 'Room',
            self::Restaurant => 'Restaurant',
            self::Laundry => 'Laundry',
            self::MiniBar => 'Mini Bar',
            self::RoomService => 'Room Service',
            self::Spa => 'Spa',
            self::Transport => 'Transport',
            self::Damage => 'Damage',
            self::Conference => 'Conference',
            self::EquipmentHire => 'Equipment Hire',
            self::ExtraBed => 'Extra Bed',
            self::EarlyCheckIn => 'Early Check In',
            self::LateCheckOut => 'Late Check Out',
            self::Miscellaneous => 'Miscellaneous',
        };
    }
}
