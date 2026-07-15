<?php

use App\Models\Setting;

if (!function_exists('app_currency')) {
    function app_currency(): string
    {
        try {
            return Setting::getInstance()->currency ?? 'TSH';
        } catch (Throwable) {
            return 'TSH';
        }
    }
}

if (!function_exists('format_money')) {
    function format_money(float|int|string|null $amount, int $decimals = 2): string
    {
        $amount = is_numeric($amount) ? (float) $amount : 0;
        return number_format($amount, $decimals) . ' ' . app_currency();
    }
}
