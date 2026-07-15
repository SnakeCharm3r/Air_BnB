<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    public function guardName(): string
    {
        return 'web';
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'full_name',
        'failed_attempts',
        'locked_until',
        'status',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'locked_until'      => 'datetime',
            'last_login_at'     => 'datetime',
        ];
    }

    public function tokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function createdBookings()
    {
        return $this->hasMany(Booking::class, 'created_by');
    }

    public function postedCharges()
    {
        return $this->hasMany(BookingCharge::class, 'posted_by');
    }

    public function approvedPayments()
    {
        return $this->hasMany(Payment::class, 'approved_by');
    }

    public function issuedInvoices()
    {
        return $this->hasMany(Invoice::class, 'issued_by');
    }

    public function closedFolios()
    {
        return $this->hasMany(GuestFolio::class, 'closed_by');
    }

    /**
     * Check if user is locked
     */
    public function isLocked(): bool
    {
        if ($this->status === 'locked') {
            return true;
        }
        
        if ($this->locked_until && $this->locked_until->isFuture()) {
            return true;
        }
        
        return false;
    }

    /**
     * Record failed login attempt
     */
    public function recordFailedAttempt(int $maxAttempts = 3, int $lockoutMinutes = 30): void
    {
        $this->failed_attempts++;
        
        if ($this->failed_attempts >= $maxAttempts) {
            $this->status = 'locked';
            $this->locked_until = Carbon::now()->addMinutes($lockoutMinutes);
        }
        
        $this->save();
    }

    /**
     * Reset failed attempts on successful login
     */
    public function resetFailedAttempts(): void
    {
        $this->failed_attempts = 0;
        $this->locked_until = null;
        $this->status = 'active';
        $this->last_login_at = Carbon::now();
        $this->save();
    }

    /**
     * Unlock user account
     */
    public function unlock(): void
    {
        $this->failed_attempts = 0;
        $this->locked_until = null;
        $this->status = 'active';
        $this->save();
    }

    /**
     * Check if user is currently active (logged in within last 15 minutes)
     */
    public function isActive(): bool
    {
        if (!$this->last_login_at) {
            return false;
        }

        return $this->last_login_at->diffInMinutes(Carbon::now()) < 15;
    }

    public function hasLegacyRole(string $role): bool
    {
        return strtolower($this->role ?? '') === strtolower($role);
    }

    public function isAdmin(): bool
    {
        return $this->hasLegacyRole('admin') || $this->hasLegacyRole('owner');
    }

    public function isManager(): bool
    {
        return $this->hasLegacyRole('manager') || $this->isAdmin();
    }

    public function isReceptionist(): bool
    {
        return $this->hasLegacyRole('receptionist') || $this->isManager();
    }

    public function isChef(): bool
    {
        return $this->hasLegacyRole('chef') || $this->isManager();
    }

    public function isAdminOrManager(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }
}
