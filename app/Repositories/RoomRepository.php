<?php
// app/Repositories/RoomRepository.php
namespace App\Repositories;

use App\Models\{Room, RoomType, BlockedDate};
use Carbon\Carbon;

class RoomRepository
{
    public function create(array $data): Room
    {
        $data['room_number'] = strtoupper($data['room_number']);
        $data['status'] = 'available';
        return Room::create($data);
    }

    public function update(int $id, array $data): Room
    {
        $room = Room::findOrFail($id);
        $room->update($data);
        return $room->fresh();
    }

    public function getRoomsWithFilters(array $filters)
    {
        $query = Room::with('roomType');

        if (!empty($filters['room_type_id'])) {
            $query->where('room_type_id', $filters['room_type_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['floor'])) {
            $query->where('floor', $filters['floor']);
        }

        return $query->orderBy('room_number')->get();
    }

    public function getRoomTypes()
    {
        return RoomType::with(['rooms'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($roomType) {
                return [
                    'id' => $roomType->id,
                    'name' => $roomType->name,
                    'slug' => $roomType->slug,
                    'base_daily_rate' => $roomType->base_daily_rate,
                    'rate_7plus_days' => $roomType->rate_7plus_days,
                    'rate_10plus_days' => $roomType->rate_10plus_days,
                    'monthly_package_price' => $roomType->monthly_package_price,
                    'monthly_custom_discount_enabled' => $roomType->monthly_custom_discount_enabled,
                    'max_capacity' => $roomType->max_capacity,
                    'amenities' => $roomType->amenities,
                    'status' => $roomType->status,
                    'rooms_count' => $roomType->rooms->count(),
                    'available_rooms_count' => $roomType->rooms->where('status', 'available')->count(),
                ];
            });
    }

    public function blockDates(array $data): int
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $blockedDates = [];

        while ($startDate <= $endDate) {
            $blockedDates[] = [
                'room_type_id' => $data['room_type_id'] ?? null,
                'date' => $startDate->format('Y-m-d'),
                'reason' => $data['reason'],
                'blocked_by' => auth()->id(),
                'is_manual' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $startDate->addDay();
        }

        BlockedDate::insert($blockedDates);
        return count($blockedDates);
    }

    public function getBlockedDates(array $filters)
    {
        $query = BlockedDate::with(['roomType', 'blockedBy']);

        if (!empty($filters['room_type_id'])) {
            $query->where('room_type_id', $filters['room_type_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date')->get();
    }
}
