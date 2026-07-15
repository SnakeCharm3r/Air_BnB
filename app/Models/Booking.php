<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'booking_ref',
        'tenant_id',
        'guest_id',
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in_date',
        'check_in_time',
        'check_out_date',
        'check_out_time',
        'adults',
        'children',
        'reservation_type',
        'expiry_date',
        'total_amount',
        'retainer_paid',
        'balance_due',
        'payment_method',
        'payment_reference',
        'company_name',
        'tax_id',
        'credit_terms_days',
        'special_requests',
        'notes',
        'created_by',
        'status',
        'is_no_show',
        'no_show_reason',
        'reminder_sent_at',
        'actual_checkout',
        'payment_status',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'expiry_date' => 'date',
        'actual_checkout' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'retainer_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'is_no_show' => 'boolean',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function folio()
    {
        return $this->hasOne(GuestFolio::class, 'booking_id');
    }

    public function charges()
    {
        return $this->hasMany(BookingCharge::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'booking_id');
    }

    public function getNightsAttribute(): int
    {
        if (! $this->check_in_date || ! $this->check_out_date) {
            return 1;
        }

        return max(1, now()->parse($this->check_in_date)->diffInDays(now()->parse($this->check_out_date)));
    }

    public function isNoShow(): bool
    {
        return $this->status === 'no_show' || $this->is_no_show;
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['confirmed', 'checked_in'], true);
    }
}
