<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'phone',
        'payment_type',
        'salary_amount',
        'daily_rate',
        'delivery_rate',
        'hourly_rate',
        'leave_quota',
        'pay_date',
        'balance',
        'is_active',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'balance' => 'decimal:2',
            'salary_amount' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'delivery_rate' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'leave_quota' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            'monthly' => 'Bulanan',
            'daily' => 'Harian',
            'hourly' => 'Per Jam',
            'per_delivery' => 'Per Pengantaran',
            default => $this->payment_type,
        };
    }

    public function getRateLabelAttribute(): string
    {
        return match ($this->payment_type) {
            'monthly' => $this->salary_amount ? 'Rp ' . number_format($this->salary_amount, 0, ',', '.') : '-',
            'daily' => $this->daily_rate ? 'Rp ' . number_format($this->daily_rate, 0, ',', '.') . ' /hari' : '-',
            'hourly' => $this->hourly_rate ? 'Rp ' . number_format($this->hourly_rate, 0, ',', '.') . ' /jam' : '-',
            'per_delivery' => $this->delivery_rate ? 'Rp ' . number_format($this->delivery_rate, 0, ',', '.') . ' /pengantaran' : '-',
            default => '-',
        };
    }

    public function getLeaveTakenAttribute(): int
    {
        return $this->leaves()->year()->count();
    }

    public function getLeaveRemainingAttribute(): int
    {
        return max(0, $this->leave_quota - $this->leave_taken);
    }
}
