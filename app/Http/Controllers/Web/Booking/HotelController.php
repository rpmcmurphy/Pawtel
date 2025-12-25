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
        $roomTypes = $this->bookingService->getRoomTypes();

        return view('booking.hotel.index', [
            'roomTypes' => $roomTypes['success'] ? $roomTypes['data'] : []
        ]);
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
            'room_type_id' => 'required|integer|exists:room_types,id',
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

        // Handle nested response structure
        $roomTypesData = $roomTypes['success'] ? $roomTypes['data'] : [];
        $roomTypesList = isset($roomTypesData['data']) ? $roomTypesData['data'] : (isset($roomTypesData[0]) ? $roomTypesData : []);
        
        $addonData = $addonServices['success'] ? $addonServices['data'] : [];
        $addons = isset($addonData['data']) ? $addonData['data'] : (isset($addonData[0]) ? $addonData : []);

        return view('booking.hotel.booking-form', [
            'roomTypes' => $roomTypesList,
            'addonServices' => $addons,
            'bookingParams' => session('booking_params', []),
            'availability' => session('availability', [])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'special_requests' => 'nullable|string|max:1000',
            'addons' => 'nullable|array',
            'addons.*' => 'integer|exists:addon_services,id',
        ]);

        $bookingData = $request->only([
            'check_in_date',
            'check_out_date',
            'room_type_id',
            'special_requests',
        ]);

        // Transform addons array from [1, 2] to [{addon_service_id: 1, quantity: 1}, ...]
        if ($request->has('addons') && is_array($request->addons)) {
            $bookingData['addons'] = array_map(function($addonId) {
                return [
                    'addon_service_id' => (int)$addonId,
                    'quantity' => 1
                ];
            }, $request->addons);
        }

        $response = $this->bookingService->createHotelBooking($bookingData);

        if ($response['success']) {
            // Handle nested response structure from API
            $bookingData = $response['data'];
            if (isset($bookingData['data']) && isset($bookingData['data']['id'])) {
                $bookingId = $bookingData['data']['id'];
            } elseif (isset($bookingData['id'])) {
                $bookingId = $bookingData['id'];
            } else {
                // Fallback: try to get booking number instead
                $bookingNumber = $bookingData['data']['booking_number'] ?? $bookingData['booking_number'] ?? null;
                if ($bookingNumber) {
                    // Get booking by number
                    $booking = $this->bookingService->getBookings(['booking_number' => $bookingNumber]);
                    if ($booking['success'] && !empty($booking['data']['data'])) {
                        $bookingId = $booking['data']['data'][0]['id'] ?? null;
                    }
                }
            }

            if (isset($bookingId)) {
                return redirect()->route('booking.hotel.confirmation', $bookingId)
                    ->with('success', 'Hotel booking created successfully!');
            } else {
                // Fallback: redirect to bookings list
                return redirect()->route('account.bookings')
                    ->with('success', 'Hotel booking created successfully!');
            }
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
