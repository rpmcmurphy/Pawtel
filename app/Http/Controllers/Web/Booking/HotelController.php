<?php

namespace App\Http\Controllers\Web\Booking;

use App\Http\Controllers\Controller;
use App\Services\Web\BookingService;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        return view('booking.hotel.index');
    }

    public function rooms()
    {
        $roomTypes = $this->bookingService->getRoomTypes();

        return view('booking.hotel.rooms', [
            'roomTypes' => $roomTypes['success'] ? $roomTypes['data'] : []
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type_id' => 'required|integer',
        ]);

        $params = $request->only(['check_in_date', 'check_out_date', 'room_type_id']);
        $availability = $this->bookingService->checkAvailability($params);

        if ($request->expectsJson()) {
            return response()->json($availability);
        }

        return redirect()->route('booking.hotel.form')
            ->with('availability', $availability)
            ->with('booking_params', $params);
    }

    public function showBookingForm(Request $request)
    {
        // Get room types and addon services
        $roomTypes = $this->bookingService->getRoomTypes();
        $addonServices = $this->bookingService->getAddonServices('hotel');

        return view('booking.hotel.booking-form', [
            'roomTypes' => $roomTypes['success'] ? $roomTypes['data'] : [],
            'addonServices' => $addonServices['success'] ? $addonServices['data'] : [],
            'bookingParams' => session('booking_params', []),
            'availability' => session('availability', [])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type_id' => 'required|integer',
            'special_requests' => 'nullable|string|max:1000',
            'addons' => 'nullable|array',
            'addons.*' => 'integer',
        ]);

        $bookingData = $request->only([
            'check_in_date',
            'check_out_date',
            'room_type_id',
            'special_requests',
            'addons'
        ]);

        $response = $this->bookingService->createHotelBooking($bookingData);

        if ($response['success']) {
            return redirect()->route('booking.hotel.confirmation', $response['data']['id'])
                ->with('success', 'Hotel booking created successfully!');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to create booking.')
            ->withInput();
    }

    public function confirmation($id)
    {
        $booking = $this->bookingService->getBooking($id);

        if (!$booking['success']) {
            return redirect()->route('booking.hotel.index')
                ->with('error', 'Booking not found.');
        }

        return view('booking.hotel.confirmation', [
            'booking' => $booking['data']
        ]);
    }
}
