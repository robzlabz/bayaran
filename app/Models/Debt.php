<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'paid_amount',
        'description',
        'debt_date',
        'is_paid',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'is_paid' => 'boolean',
            'debt_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class, 'debt_payment')->withPivot('amount')->withTimestamps();
    }

    public function getRemainingAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }
}
