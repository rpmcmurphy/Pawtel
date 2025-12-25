<?php

namespace App\Http\Controllers\Web\Booking;

use App\Http\Controllers\Controller;
use App\Services\Web\BookingService;
use Illuminate\Http\Request;

class SpaController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        return view('booking.spa.index');
    }

    public function packages()
    {
        $spaPackages = $this->bookingService->getSpaPackages();

        return view('booking.spa.packages', [
            'packages' => $spaPackages['success'] ? $spaPackages['data'] : []
        ]);
    }

    public function showBookingForm(Request $request)
    {
        $spaPackages = $this->bookingService->getSpaPackages();
        $addonServices = $this->bookingService->getAddonServices('spa');

        // Handle nested response structure
        $packagesData = $spaPackages['success'] ? $spaPackages['data'] : [];
        $packages = isset($packagesData['data']) ? $packagesData['data'] : (isset($packagesData[0]) ? $packagesData : []);
        
        $addonData = $addonServices['success'] ? $addonServices['data'] : [];
        $addons = isset($addonData['data']) ? $addonData['data'] : (isset($addonData[0]) ? $addonData : []);

        return view('booking.spa.booking-form', [
            'packages' => $packages,
            'addonServices' => $addons
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'spa_package_id' => 'required|integer',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string',
            'special_requests' => 'nullable|string|max:1000',
            'addons' => 'nullable|array',
        ]);

        $bookingData = $request->only([
            'spa_package_id',
            'appointment_date',
            'appointment_time',
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

        $response = $this->bookingService->createSpaBooking($bookingData);

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
                return redirect()->route('booking.spa.confirmation', $bookingId)
                    ->with('success', 'Spa booking created successfully!');
            } else {
                // Fallback: redirect to bookings list
                return redirect()->route('account.bookings')
                    ->with('success', 'Spa booking created successfully!');
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
            return redirect()->route('booking.spa.index')
                ->with('error', 'Booking not found.');
        }

        // Handle nested response structure
        $bookingData = $booking['data'];
        $bookingInfo = isset($bookingData['data']) ? $bookingData['data'] : $bookingData;

        return view('booking.spa.confirmation', [
            'booking' => $bookingInfo
        ]);
    }
}
