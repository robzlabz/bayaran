<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'topup' => 'Top Up',
            'transport' => 'Ongkos',
            'salary' => 'Gaji',
            'adjustment' => 'Penyesuaian',
            default => ucfirst($this->type),
        };
    }

    public function getAmountFormattedAttribute(): string
    {
        $prefix = $this->amount >= 0 ? '+' : '';
        return $prefix . 'Rp ' . number_format(abs($this->amount), 0, ',', '.');
    }
}
