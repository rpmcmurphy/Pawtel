<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateRoomRequest;
use App\Repositories\RoomRepository;
use App\Models\{RoomType, Room};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Str;

class RoomManagementController extends Controller
{
    public function __construct(
        private RoomRepository $roomRepo
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $rooms = $this->roomRepo->getRoomsWithFilters(
                $request->only(['room_type_id', 'status', 'floor'])
            );

            return response()->json([
                'success' => true,
                'data' => $rooms->map(function ($room) {
                    return [
                        'id' => $room->id,
                        'room_number' => $room->room_number,
                        'floor' => $room->floor,
                        'status' => $room->status,
                        'notes' => $room->notes,
                        'room_type' => [
                            'id' => $room->roomType->id,
                            'name' => $room->roomType->name,
                        ],
                        'created_at' => $room->created_at->format('Y-m-d H:i:s'),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rooms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateRoomRequest $request): JsonResponse
    {
        try {
            $room = $this->roomRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Room created successfully',
                'data' => $room->load('roomType')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $room = $this->roomRepo->update($id, $request->only(['status', 'notes']));

            return response()->json([
                'success' => true,
                'message' => 'Room status updated successfully',
                'data' => $room->load('roomType')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found',
            ], 404);
        }
    }

    public function roomTypes(): JsonResponse
    {
        try {
            $roomTypes = $this->roomRepo->getRoomTypes();
            return response()->json(['success' => true, 'data' => $roomTypes]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch room types'], 500);
        }
    }

    public function blockDates(Request $request): JsonResponse
    {
        $request->validate([
            'room_type_id' => 'nullable|exists:room_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $count = $this->roomRepo->blockDates($request->only([
                'room_type_id',
                'start_date',
                'end_date',
                'reason'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Dates blocked successfully',
                'data' => [
                    'blocked_dates_count' => $count,
                    'date_range' => $request->start_date . ' to ' . $request->end_date,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to block dates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBlockedDates(Request $request): JsonResponse
    {
        try {
            $blockedDates = $this->roomRepo->getBlockedDates(
                $request->only(['room_type_id', 'date_from', 'date_to'])
            );

            return response()->json([
                'success' => true,
                'data' => $blockedDates->map(function ($blocked) {
                    return [
                        'id' => $blocked->id,
                        'date' => $blocked->date->format('Y-m-d'),
                        'reason' => $blocked->reason,
                        'is_manual' => $blocked->is_manual,
                        'room_type' => $blocked->roomType ? [
                            'id' => $blocked->roomType->id,
                            'name' => $blocked->roomType->name,
                        ] : null,
                        'blocked_by' => $blocked->blockedBy ? $blocked->blockedBy->name : null,
                        'created_at' => $blocked->created_at->format('Y-m-d H:i:s'),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blocked dates',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
