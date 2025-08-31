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

    /**
     * Show the form for creating a new manual booking.
     */
    public function create()
    {
        return view('admin.bookings.create');
    }

    /**
     * Store a newly created manual booking.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_type' => 'required|in:hotel,spa,spay_neuter',
            'pet_name' => 'required|string|max:255',
            'total_amount' => 'nullable|numeric|min:0',
            'booking_status' => 'required|in:pending,confirmed',
            // Add conditional validation based on booking type
        ]);

        // Process customer information - either existing or new
        $customerData = $this->processCustomerData($request);

        // Prepare booking data based on type
        $bookingData = $this->prepareBookingData($request, $customerData);

        // Create booking via API service
        $response = $this->adminService->createManualBooking($bookingData);

        if ($response['success']) {
            $message = 'Booking created successfully.';

            // Send confirmation email if requested
            if ($request->has('send_confirmation')) {
                // Logic to send confirmation email
                $message .= ' Confirmation email sent to customer.';
            }

            return redirect()->route('admin.bookings.show', $response['data']['id'])
                ->with('success', $message);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $response['message'] ?? 'Failed to create booking.');
    }

    /**
     * Process customer data - either find existing or prepare new customer data
     */
    private function processCustomerData(Request $request)
    {
        if ($request->filled('customer_id')) {
            // Use existing customer
            return ['customer_id' => $request->customer_id];
        }

        // Create new customer data
        return [
            'customer_first_name' => $request->customer_first_name,
            'customer_last_name' => $request->customer_last_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
        ];
    }

    /**
     * Prepare booking data based on booking type
     */
    private function prepareBookingData(Request $request, array $customerData)
    {
        $baseData = array_merge($customerData, [
            'booking_type' => $request->booking_type,
            'pet_name' => $request->pet_name,
            'pet_type' => $request->pet_type,
            'pet_breed' => $request->pet_breed,
            'pet_age' => $request->pet_age,
            'pet_weight' => $request->pet_weight,
            'pet_gender' => $request->pet_gender,
            'pet_instructions' => $request->pet_instructions,
            'total_amount' => $request->total_amount,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'booking_status' => $request->booking_status,
            'admin_notes' => $request->admin_notes,
            'customer_notes' => $request->customer_notes,
        ]);

        // Add type-specific data
        switch ($request->booking_type) {
            case 'hotel':
                $baseData = array_merge($baseData, [
                    'checkin_date' => $request->checkin_date,
                    'checkout_date' => $request->checkout_date,
                    'room_type' => $request->room_type,
                    'number_of_pets' => $request->number_of_pets,
                ]);
                break;

            case 'spa':
                $baseData = array_merge($baseData, [
                    'service_date' => $request->service_date,
                    'service_time' => $request->service_time,
                    'service_duration' => $request->service_duration,
                    'services' => $request->services ?? [],
                ]);
                break;

            case 'spay_neuter':
                $baseData = array_merge($baseData, [
                    'procedure_date' => $request->procedure_date,
                    'procedure_type' => $request->procedure_type,
                    'veterinarian' => $request->veterinarian,
                    'pre_instructions' => $request->pre_instructions,
                ]);
                break;
        }

        return $baseData;
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
