<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time_slot',
        'available_slots',
        'booked_slots',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->whereColumn('booked_slots', '<', 'available_slots');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    // Methods
    public function hasAvailability(): bool
    {
        return $this->booked_slots < $this->available_slots;
    }

    public function getRemainingSlots(): int
    {
        return max(0, $this->available_slots - $this->booked_slots);
    }

    public function incrementBooked(): void
    {
        $this->increment('booked_slots');
    }

    public function decrementBooked(): void
    {
        if ($this->booked_slots > 0) {
            $this->decrement('booked_slots');
        }
    }
}
