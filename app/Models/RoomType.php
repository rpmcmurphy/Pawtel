<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_daily_rate',
        'rate_7plus_days',
        'rate_10plus_days',
        'monthly_package_price',
        'monthly_custom_discount_enabled',
        'max_capacity',
        'amenities',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'amenities' => 'array',
        'base_daily_rate' => 'decimal:2',
        'rate_7plus_days' => 'decimal:2',
        'rate_10plus_days' => 'decimal:2',
        'monthly_package_price' => 'decimal:2',
        'monthly_custom_discount_enabled' => 'boolean',
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
            $bookedRoomIds = DB::table('booking_rooms')
                ->join('bookings', 'booking_rooms.booking_id', '=', 'bookings.id')
                ->where('bookings.status', '!=', 'cancelled')
                ->where('bookings.check_in_date', '<=', $date)
                ->where('bookings.check_out_date', '>=', $date)
                ->pluck('booking_rooms.room_id');

            $query->whereNotIn('id', $bookedRoomIds);
        }

        return $query->count();
    }

    public function getPriceForDuration($days, $customMonthlyDiscount = null): float
    {
        // Monthly package pricing (fixed price, with optional custom admin discount)
        if ($days >= 30 && $this->monthly_package_price) {
            if ($this->monthly_custom_discount_enabled && $customMonthlyDiscount !== null) {
                return $this->monthly_package_price - $customMonthlyDiscount;
            }
            return $this->monthly_package_price;
        }
        
        // Tiered per-day pricing
        // 10+ days: use rate_10plus_days per day
        if ($days >= 10 && $this->rate_10plus_days) {
            return $this->rate_10plus_days * $days;
        }
        
        // 7+ days: use rate_7plus_days per day
        if ($days >= 7 && $this->rate_7plus_days) {
            return $this->rate_7plus_days * $days;
        }
        
        // Default: base daily rate
        return $this->base_daily_rate * $days;
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
