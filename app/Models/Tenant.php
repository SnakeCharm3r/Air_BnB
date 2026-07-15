<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'currency',
        'timezone',
        'tax_rate',
        'logo',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tenant_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'tenant_id');
    }

    public function folios()
    {
        return $this->hasMany(GuestFolio::class, 'tenant_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tenant_id');
    }
}
