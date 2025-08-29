<?php

namespace App\Repositories;

use App\Models\Booking;
use Carbon\Carbon;

class BookingRepository
{
    public function findOrFail(int $id): Booking
    {
        return Booking::findOrFail($id);
    }

    public function findWithRelations(int $id, array $relations = []): Booking
    {
        return Booking::with($relations)->findOrFail($id);
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function update(int $id, array $data): Booking
    {
        $booking = $this->findOrFail($id);
        $booking->update($data);
        return $booking->fresh();
    }

    public function count(): int
    {
        return Booking::count();
    }

    public function countByStatus(string $status): int
    {
        return Booking::where('status', $status)->count();
    }

    public function countForDate(Carbon $date): int
    {
        return Booking::whereDate('created_at', $date)->count();
    }

    public function getRevenueForDate(Carbon $date): float
    {
        return Booking::whereDate('created_at', $date)
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('final_amount');
    }

    public function getRevenueForPeriod(Carbon $start, Carbon $end): float
    {
        return Booking::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('final_amount');
    }

    public function getCountByType(): array
    {
        return Booking::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->toArray();
    }

    public function getCountByStatus(): array
    {
        return Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->toArray();
    }

    public function getWithFilters(array $filters, int $perPage = 15)
    {
        $query = Booking::with(['user', 'roomType', 'spaBooking.spaPackage', 'spayBooking.spayPackage']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('check_in_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('check_out_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
