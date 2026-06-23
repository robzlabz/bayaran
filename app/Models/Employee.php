<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
