<?php

namespace App\Http\Controllers\Web\Booking;

use App\Http\Controllers\Controller;
use App\Services\Web\BookingService;
use Illuminate\Http\Request;

class SpayController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $spayPackages = $this->bookingService->getSpayPackages();

        return view('booking.spay.index', [
            'packages' => $spayPackages['success'] ? $spayPackages['data'] : []
        ]);
    }

    public function showBookingForm(Request $request)
    {
        $spayPackages = $this->bookingService->getSpayPackages();

        // Handle nested response structure
        $packagesData = $spayPackages['success'] ? $spayPackages['data'] : [];
        $packages = isset($packagesData['data']) ? $packagesData['data'] : (isset($packagesData[0]) ? $packagesData : []);

        return view('booking.spay.booking-form', [
            'packages' => $packages
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'spay_package_id' => 'required|integer',
            'preferred_date' => 'required|date|after_or_equal:today',
            'cat_age' => 'required|integer|min:3',
            'cat_weight' => 'required|numeric|min:1',
            'medical_conditions' => 'nullable|string|max:1000',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        // Map web form fields to API expected fields
        $bookingData = [
            'spay_package_id' => $request->spay_package_id,
            'procedure_date' => $request->preferred_date, // Map preferred_date to procedure_date
            'pet_name' => 'Pet', // Default pet name, can be updated later
            'pet_age' => $request->cat_age,
            'pet_weight' => $request->cat_weight,
            'medical_notes' => $request->medical_conditions, // Map medical_conditions to medical_notes
            'special_requests' => $request->special_requests,
        ];

        // Transform addons array if provided
        if ($request->has('addons') && is_array($request->addons)) {
            $bookingData['addons'] = array_map(function($addonId) {
                return [
                    'addon_service_id' => (int)$addonId,
                    'quantity' => 1
                ];
            }, $request->addons);
        }

        $response = $this->bookingService->createSpayBooking($bookingData);

        if ($response['success']) {
            // Handle nested response structure from API
            $bookingData = $response['data'];
            $bookingId = null;
            
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
                return redirect()->route('booking.spay.confirmation', $bookingId)
                    ->with('success', 'Spay/Neuter booking created successfully!');
            } else {
                // Fallback: redirect to bookings list
                return redirect()->route('account.bookings')
                    ->with('success', 'Spay/Neuter booking created successfully!');
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
            return redirect()->route('booking.spay.index')
                ->with('error', 'Booking not found.');
        }

        // Handle nested response structure
        $bookingData = $booking['data'];
        $bookingInfo = isset($bookingData['data']) ? $bookingData['data'] : $bookingData;

        return view('booking.spay.confirmation', [
            'booking' => $bookingInfo
        ]);
    }
}
