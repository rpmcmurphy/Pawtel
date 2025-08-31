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

        return view('booking.spay.booking-form', [
            'packages' => $spayPackages['success'] ? $spayPackages['data'] : []
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

        $bookingData = $request->only([
            'spay_package_id',
            'preferred_date',
            'cat_age',
            'cat_weight',
            'medical_conditions',
            'special_requests'
        ]);

        $response = $this->bookingService->createSpayBooking($bookingData);

        if ($response['success']) {
            return redirect()->route('account.bookings')
                ->with('success', 'Spay/Neuter booking created successfully!');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to create booking.')
            ->withInput();
    }
}
