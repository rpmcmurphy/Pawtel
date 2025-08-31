<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['status', 'type', 'search', 'date_from', 'date_to', 'page']);
        $bookings = $this->adminService->getAdminBookings($params);

        return view('admin.bookings.index', [
            'bookings' => $bookings['success'] ? $bookings['data'] : [],
            'filters' => $params
        ]);
    }

    public function byType(Request $request, $type)
    {
        $params = $request->only(['status', 'search', 'date_from', 'date_to', 'page']);
        $params['type'] = $type;

        $bookings = $this->adminService->getAdminBookings($params);

        return view('admin.bookings.index', [
            'bookings' => $bookings['success'] ? $bookings['data'] : [],
            'filters' => $params,
            'currentType' => $type
        ]);
    }

    public function show($id)
    {
        $booking = $this->adminService->getAdminBooking($id);

        if (!$booking['success']) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Booking not found.');
        }

        return view('admin.bookings.show', [
            'booking' => $booking['data']
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:1000'
        ]);

        $response = $this->adminService->updateBookingStatus(
            $id,
            $request->status,
            $request->notes
        );

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Booking status updated successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to update booking status.');
    }

    public function confirm(Request $request, $id)
    {
        $response = $this->adminService->confirmBooking($id);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Booking confirmed successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to confirm booking.');
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $response = $this->adminService->cancelBooking($id, $request->reason);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Booking cancelled successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to cancel booking.');
    }
}
