<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MenuItem extends Model
{
    protected $fillable = [
        'tenant_id',
        'iptv_menu_item_id',
        'inventory_item_id',
        'name',
        'categories',
        'description',
        'price',
        'unit',
        'image',
        'available',
        'available_from',
        'available_until',
        'requires_chef',
    ];

    protected $casts = [
        'available' => 'boolean',
        'requires_chef' => 'boolean',
        'price' => 'decimal:2',
        'available_from' => 'datetime:H:i',
        'available_until' => 'datetime:H:i',
        'categories' => 'array',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function isAvailableNow(): bool
    {
        if (!$this->available) {
            return false;
        }

        $now = now();
        $timeStr = $now->format('H:i:s');

        if (!$this->isWithinServingWindows($timeStr)) {
            return false;
        }

        if (!KitchenHour::isKitchenOpenToday($now)) {
            return false;
        }

        if ($this->requires_chef && !static::isChefOnDuty($now)) {
            return false;
        }

        return true;
    }

    public function isWithinServingWindows($timeStr): bool
    {
        $categories = $this->categories ?? [];
        if (empty($categories)) {
            return true;
        }

        foreach ($categories as $category) {
            $window = $this->servingWindowForCategory($category);
            if ($timeStr >= $window['from'] && $timeStr <= $window['until']) {
                return true;
            }
        }

        return false;
    }

    public function servingWindowForCategory($category): array
    {
        $categoryWindow = static::categoryAvailabilityWindow($category);
        $kitchen = KitchenHour::getGlobalHours();

        if (!$kitchen || $kitchen->is_closed) {
            return ['from' => '00:00:00', 'until' => '00:00:00'];
        }

        $kitchenFrom = $kitchen->open_time ? $kitchen->open_time->format('H:i:s') : '00:00:00';
        $kitchenUntil = $kitchen->close_time ? $kitchen->close_time->format('H:i:s') : '23:59:59';

        $from = max($categoryWindow['from'], $kitchenFrom);
        $until = min($categoryWindow['until'], $kitchenUntil);

        if ($from > $until) {
            return ['from' => '00:00:00', 'until' => '00:00:00'];
        }

        return ['from' => $from, 'until' => $until];
    }

    public function derivedServingWindow(): array
    {
        $categories = $this->categories ?? [];
        if (empty($categories)) {
            $kitchen = KitchenHour::getGlobalHours();
            return $kitchen && !$kitchen->is_closed
                ? ['from' => $kitchen->open_time->format('H:i:s'), 'until' => $kitchen->close_time->format('H:i:s')]
                : ['from' => '00:00:00', 'until' => '00:00:00'];
        }

        $from = '23:59:59';
        $until = '00:00:00';
        $hasWindow = false;

        foreach ($categories as $category) {
            $window = $this->servingWindowForCategory($category);
            if ($window['from'] === '00:00:00' && $window['until'] === '00:00:00') {
                continue;
            }
            if ($window['from'] < $from) {
                $from = $window['from'];
            }
            if ($window['until'] > $until) {
                $until = $window['until'];
            }
            $hasWindow = true;
        }

        return $hasWindow ? ['from' => $from, 'until' => $until] : ['from' => '00:00:00', 'until' => '00:00:00'];
    }

    public static function categoryAvailabilityWindow($category): array
    {
        return match (strtolower($category)) {
            'breakfast' => ['from' => '07:00:00', 'until' => '10:30:00'],
            'lunch', 'dinner' => ['from' => '12:00:00', 'until' => '22:30:00'],
            default => ['from' => '00:00:00', 'until' => '23:59:59'],
        };
    }

    public static function defaultAvailabilityForCategories($categories): array
    {
        $from = '23:59:59';
        $until = '00:00:00';
        $hasWindow = false;

        foreach ($categories as $category) {
            $window = static::categoryAvailabilityWindow($category);
            if ($window['from'] < $from) {
                $from = $window['from'];
            }
            if ($window['until'] > $until) {
                $until = $window['until'];
            }
            $hasWindow = true;
        }

        return $hasWindow ? ['from' => $from, 'until' => $until] : ['from' => '00:00:00', 'until' => '23:59:59'];
    }

    public static function isChefOnDuty($time = null): bool
    {
        $time = $time ?: now();
        $day = strtolower($time->format('l'));
        $timeStr = $time->format('H:i:s');

        return DB::table('duty_schedules')
            ->join('staff', 'duty_schedules.staff_id', '=', 'staff.id')
            ->where('duty_schedules.day_of_week', $day)
            ->where('duty_schedules.start_time', '<=', $timeStr)
            ->where('duty_schedules.end_time', '>=', $timeStr)
            ->where('staff.is_active', 1)
            ->where(function ($query) {
                $query->where('staff.role', 'like', '%chef%')
                      ->orWhere('staff.role', 'like', '%kitchen%');
            })
            ->exists();
    }
}
