<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpaPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_minutes',
        'price',
        'resident_price',
        'max_daily_bookings',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'resident_price' => 'decimal:2',
    ];

    // Relationships
    public function spaBookings(): HasMany
    {
        return $this->hasMany(SpaBooking::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Methods
    public function getAvailableSlotsForDate($date): int
    {
        $bookedSlots = $this->spaBookings()
            ->where('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'completed'])
            ->count();

        return max(0, $this->max_daily_bookings - $bookedSlots);
    }

    public function isAvailableForDate($date): bool
    {
        return $this->getAvailableSlotsForDate($date) > 0;
    }
}
