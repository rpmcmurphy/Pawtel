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

        return view('booking.spa.booking-form', [
            'packages' => $spaPackages['success'] ? $spaPackages['data'] : [],
            'addonServices' => $addonServices['success'] ? $addonServices['data'] : []
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
            'addons'
        ]);

        $response = $this->bookingService->createSpaBooking($bookingData);

        if ($response['success']) {
            return redirect()->route('account.bookings')
                ->with('success', 'Spa booking created successfully!');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to create booking.')
            ->withInput();
    }
}
