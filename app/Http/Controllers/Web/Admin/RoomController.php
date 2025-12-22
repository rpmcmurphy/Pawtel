<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['room_type_id', 'status', 'floor']);
        $rooms = $this->adminService->get('admin/rooms', $params);
        $roomTypes = $this->adminService->get('admin/rooms/types/list');

        return view('admin.rooms.index', [
            'rooms' => $rooms['success'] ? ($rooms['data']['data'] ?? $rooms['data']) : [],
            'roomTypes' => $roomTypes['success'] ? ($roomTypes['data']['data'] ?? $roomTypes['data']) : [],
            'filters' => $params
        ]);
    }

    public function create()
    {
        $roomTypes = $this->adminService->get('admin/rooms/types/list');

        return view('admin.rooms.create', [
            'roomTypes' => $roomTypes['success'] ? ($roomTypes['data']['data'] ?? $roomTypes['data']) : []
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1|max:10',
            'status' => 'required|in:available,occupied,maintenance',
            'notes' => 'nullable|string|max:500'
        ]);

        $response = $this->adminService->post('admin/rooms', $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.rooms.index')
                ->with('success', $apiResponse['message'] ?? 'Room created successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to create room.');
    }

    public function show($id)
    {
        $room = $this->adminService->get("admin/rooms/{$id}");

        if (!$room['success']) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Room not found.');
        }

        $roomData = $room['data']['data'] ?? $room['data'];

        return view('admin.rooms.show', [
            'room' => $roomData
        ]);
    }

    public function edit($id)
    {
        $room = $this->adminService->get("admin/rooms/{$id}");
        $roomTypes = $this->adminService->get('admin/rooms/types/list');

        if (!$room['success']) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Room not found.');
        }

        return view('admin.rooms.edit', [
            'room' => $room['data']['data'] ?? $room['data'],
            'roomTypes' => $roomTypes['success'] ? ($roomTypes['data']['data'] ?? $roomTypes['data']) : []
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1|max:10',
            'status' => 'required|in:available,occupied,maintenance',
            'notes' => 'nullable|string|max:500'
        ]);

        $response = $this->adminService->put("admin/rooms/{$id}", $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.rooms.show', $id)
                ->with('success', $apiResponse['message'] ?? 'Room updated successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to update room.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
            'notes' => 'nullable|string|max:500'
        ]);

        $response = $this->adminService->put("admin/rooms/{$id}/status", $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->back()
                ->with('success', $apiResponse['message'] ?? 'Room status updated successfully.');
        }

        return redirect()->back()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to update room status.');
    }

    public function blockDates(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'nullable|exists:room_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255'
        ]);

        $response = $this->adminService->post('admin/rooms/block-dates', $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->back()
                ->with('success', $apiResponse['message'] ?? 'Dates blocked successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to block dates.');
    }
}

