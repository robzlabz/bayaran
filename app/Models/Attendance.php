<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'clock_in_photo',
        'clock_out_photo',
        'is_manual_entry',
        'is_clock_in_manual',
        'is_clock_out_manual',
        'notes',
        'work_hours',
    ];

    protected function casts(): array
    {
        return [
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'is_manual_entry' => 'boolean',
            'is_clock_in_manual' => 'boolean',
            'is_clock_out_manual' => 'boolean',
            'work_hours' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('clock_in', today());
    }

    public function scopeForOwner($query, $ownerId)
    {
        return $query->whereIn('employee_id', function ($q) use ($ownerId) {
            $q->select('id')->from('employees')->where('owner_id', $ownerId);
        });
    }
}
