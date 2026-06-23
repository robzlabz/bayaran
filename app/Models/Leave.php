<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'annual_leave' => 'Cuti',
            default => ucfirst($this->type),
        };
    }

    public function getDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function scopeYear($query, $year = null)
    {
        $year = $year ?? now()->year;
        return $query->whereYear('start_date', $year);
    }
}
