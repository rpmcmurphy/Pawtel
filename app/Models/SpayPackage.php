<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpayPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'resident_price',
        'post_care_days',
        'post_care_rate_first_3_days',
        'post_care_rate_next_4_days',
        'post_care_rate_second_week',
        'max_daily_slots',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'resident_price' => 'decimal:2',
        'post_care_rate_first_3_days' => 'decimal:2',
        'post_care_rate_next_4_days' => 'decimal:2',
        'post_care_rate_second_week' => 'decimal:2',
    ];

    // Relationships
    public function spayBookings(): HasMany
    {
        return $this->hasMany(SpayBooking::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function getAvailableSlotsForDate($date): int
    {
        $bookedSlots = $this->spayBookings()
            ->where('procedure_date', $date)
            ->whereIn('status', ['scheduled', 'completed'])
            ->count();

        return max(0, $this->max_daily_slots - $bookedSlots);
    }

    public function isAvailableForDate($date): bool
    {
        return $this->getAvailableSlotsForDate($date) > 0;
    }
}
