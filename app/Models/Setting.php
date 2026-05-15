<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'lodge_name',
        'lodge_logo',
        'login_logo',
        'favicon',
        'contact_email',
        'contact_phone',
        'contact_address',
        'owner_email',
        'max_login_attempts',
        'lockout_duration',
        'two_factor_auth',
        'session_timeout',
        'audit_logging',
        'notification_settings',
    ];

    protected $casts = [
        'two_factor_auth' => 'boolean',
        'audit_logging' => 'boolean',
        'notification_settings' => 'array',
    ];

    /**
     * Get the singleton settings instance
     */
    public static function getInstance(): self
    {
        return static::firstOrCreate([], [
            'lodge_name' => 'LodgeOS',
            'max_login_attempts' => 3,
            'lockout_duration' => 30,
            'two_factor_auth' => false,
            'session_timeout' => 24,
            'audit_logging' => true,
        ]);
    }
}
