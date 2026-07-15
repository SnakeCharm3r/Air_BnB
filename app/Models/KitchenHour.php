<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenHour extends Model
{
    protected $fillable = [
        'tenant_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
    ];

    public static function isKitchenOpenToday($time = null): bool
    {
        $time = $time ?: now();
        $timeStr = $time->format('H:i:s');

        $hours = static::getGlobalHours();

        if (!$hours || $hours->is_closed) {
            return false;
        }

        $open = $hours->open_time ? $hours->open_time->format('H:i:s') : '00:00:00';
        $close = $hours->close_time ? $hours->close_time->format('H:i:s') : '23:59:59';

        return $timeStr >= $open && $timeStr <= $close;
    }

    public static function getGlobalHours($tenantId = null)
    {
        return static::where('is_global', true)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    public static function seedDefaults($tenantId = null): void
    {
        static::firstOrCreate(
            ['tenant_id' => $tenantId, 'is_global' => true],
            ['day_of_week' => null, 'open_time' => '07:00:00', 'close_time' => '22:30:00', 'is_closed' => false]
        );
    }
}
