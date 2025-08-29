<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_daily_rate',
        'weekly_rate',
        'ten_day_rate',
        'monthly_rate',
        'max_capacity',
        'amenities',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'amenities' => 'array',
        'base_daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'ten_day_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
    ];

    // Relationships
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function blockedDates(): HasMany
    {
        return $this->hasMany(BlockedDate::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Methods
    public function getAvailableRoomsCount($date = null): int
    {
        $query = $this->rooms()->where('status', 'available');

        if ($date) {
            // Check for blocked dates and bookings
            $bookedRoomIds = BookingRoom::whereHas('booking', function ($q) use ($date) {
                $q->where('status', '!=', 'cancelled')
                    ->where('check_in_date', '<=', $date)
                    ->where('check_out_date', '>=', $date);
            })->pluck('room_id');

            $query->whereNotIn('id', $bookedRoomIds);
        }

        return $query->count();
    }

    public function getPriceForDuration($days): float
    {
        if ($days >= 30 && $this->monthly_rate) {
            return $this->monthly_rate;
        } elseif ($days >= 10 && $this->ten_day_rate) {
            return $this->ten_day_rate;
        } elseif ($days >= 7 && $this->weekly_rate) {
            return $this->weekly_rate;
        } else {
            return $this->base_daily_rate * $days;
        }
    }

    public function isAvailableForDateRange($startDate, $endDate): bool
    {
        $availableRooms = $this->max_capacity;

        $bookedCount = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate, $endDate])
                    ->orWhereBetween('check_out_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('check_in_date', '<=', $startDate)
                            ->where('check_out_date', '>=', $endDate);
                    });
            })
            ->count();

        $blockedCount = $this->blockedDates()
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        return ($availableRooms - $bookedCount - $blockedCount) > 0;
    }
}
