<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'total_amount',
        'salary_amount',
        'debt_amount',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'salary_amount' => 'decimal:2',
            'debt_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function debts(): BelongsToMany
    {
        return $this->belongsToMany(Debt::class, 'debt_payment')->withPivot('amount')->withTimestamps();
    }
}
