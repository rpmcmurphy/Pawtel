<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'date',
        'reason',
        'blocked_by',
        'is_manual',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'date' => 'date',
        'is_manual' => 'boolean',
    ];

    // Relationships
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    // Scopes
    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('is_manual', false);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
