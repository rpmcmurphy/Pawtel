<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpayBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'spay_package_id',
        'procedure_date',
        'pet_name',
        'pet_age',
        'pet_weight',
        'medical_notes',
        'vet_assigned',
        'status',
    ];

    protected $casts = [
        'procedure_date' => 'date',
        'pet_weight' => 'decimal:2',
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function spayPackage(): BelongsTo
    {
        return $this->belongsTo(SpayPackage::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('procedure_date', $date);
    }

    // Methods
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
