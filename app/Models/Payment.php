<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'tenant_id',
        'booking_id',
        'folio_id',
        'amount',
        'payment_method',
        'payment_type',
        'payment_date',
        'payment_status',
        'payment_gateway',
        'cashier_shift',
        'receipt_number',
        'reference',
        'currency',
        'exchange_rate',
        'bank_name',
        'mobile_provider',
        'is_split_payment',
        'parent_payment_id',
        'is_refund',
        'refunded_payment_id',
        'refund_reason',
        'approved_by',
        'approved_at',
        'is_void',
        'void_reason',
        'notes',
        'payment_for',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'is_split_payment' => 'boolean',
        'is_refund' => 'boolean',
        'is_void' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function folio()
    {
        return $this->belongsTo(GuestFolio::class, 'folio_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parentPayment()
    {
        return $this->belongsTo(self::class, 'parent_payment_id');
    }

    public function refundedPayment()
    {
        return $this->belongsTo(self::class, 'refunded_payment_id');
    }

    public function childPayments()
    {
        return $this->hasMany(self::class, 'parent_payment_id');
    }

    public function refundPayments()
    {
        return $this->hasMany(self::class, 'refunded_payment_id');
    }

    public function isSuccessful(): bool
    {
        return $this->payment_status === 'successful' && ! $this->is_void;
    }

    public function isVoid(): bool
    {
        return $this->is_void;
    }
}
