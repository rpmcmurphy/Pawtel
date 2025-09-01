<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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
        // Get data needed for the form
        $roomTypes = $this->adminService->get('availability/room-types');
        $spaPackages = $this->adminService->get('spa/packages');
        $spayPackages = $this->adminService->get('spay/packages');
        $addonServices = $this->adminService->get('addon-services');

        return view('admin.bookings.create', [
            'roomTypes' => $roomTypes['success'] ? $roomTypes['data'] : [],
            'spaPackages' => $spaPackages['success'] ? $spaPackages['data'] : [],
            'spayPackages' => $spayPackages['success'] ? $spayPackages['data'] : [],
            'addonServices' => $addonServices['success'] ? $addonServices['data'] : [],
        ]);
    }

    /**
     * Store a newly created manual booking.
     */
    public function store(Request $request)
    {
        // Basic validation first
        $validated = $request->validate([
            'type' => 'required|in:hotel,spa,spay',
            'user_id' => 'required|exists:users,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'final_amount' => 'required|numeric|min:0',
            'manual_reference' => 'required|string|max:255',
            'room_type_id' => 'required_if:type,hotel|exists:room_types,id',
            'spa_package_id' => 'required_if:type,spa|exists:spa_packages,id',
            'spay_package_id' => 'required_if:type,spay|exists:spay_packages,id',
            'special_requests' => 'nullable|string|max:1000',
            'addons' => 'sometimes|array',
        ]);

        // Prepare booking data for API
        $bookingData = [
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'final_amount' => $validated['final_amount'],
            'manual_reference' => $validated['manual_reference'],
        ];

        // Add type-specific data
        if ($validated['type'] === 'hotel' && isset($validated['room_type_id'])) {
            $bookingData['room_type_id'] = $validated['room_type_id'];
        }

        if ($validated['type'] === 'spa' && isset($validated['spa_package_id'])) {
            $bookingData['spa_package_id'] = $validated['spa_package_id'];
        }

        if ($validated['type'] === 'spay' && isset($validated['spay_package_id'])) {
            $bookingData['spay_package_id'] = $validated['spay_package_id'];
        }

        // Add optional fields
        if (!empty($validated['special_requests'])) {
            $bookingData['special_requests'] = $validated['special_requests'];
        }

        // Handle addons
        if (!empty($validated['addons'])) {
            $addons = [];
            foreach ($validated['addons'] as $addon) {
                if (!empty($addon['addon_service_id']) && !empty($addon['quantity'])) {
                    $addons[] = [
                        'addon_service_id' => (int)$addon['addon_service_id'],
                        'quantity' => (int)$addon['quantity']
                    ];
                }
            }
            $bookingData['addons'] = $addons;
        } else {
            $bookingData['addons'] = [];
        }

        // Create booking via API service
        $response = $this->adminService->createManualBooking($bookingData);

        Log::info('Manual Booking Creation Response:', $response);
        Log::info('Manual Booking Request Data:', $bookingData);

        if ($response['success']) {
            $message = 'Booking created successfully.';

            // Send confirmation email if requested
            if ($request->has('send_confirmation')) {
                $message .= ' Confirmation email sent to customer.';
            }

            return redirect()->route('admin.bookings.show', $response['data']['id'])
                ->with('success', $message);
        }

        // Better error handling
        $errorMessage = 'Failed to create booking.';

        if (isset($response['message'])) {
            $errorMessage = $response['message'];
        }

        if (isset($response['errors'])) {
            $errors = is_array($response['errors']) ? $response['errors'] : [$response['errors']];
            $errorMessage .= ' Errors: ' . implode(', ', Arr::flatten($errors));
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = $this->adminService->getAdminBooking($id);

        if (!$booking['success']) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Booking not found.');
        }

        return view('admin.bookings.show', [
            'booking' => $booking['data']['data']
        ]);
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit($id)
    {
        $booking = $this->adminService->getAdminBooking($id);

        if (!$booking['success']) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Booking not found.');
        }

        // Get data needed for the form
        $roomTypes = $this->adminService->get('availability/room-types');
        $spaPackages = $this->adminService->get('spa/packages');
        $spayPackages = $this->adminService->get('spay/packages');
        $addonServices = $this->adminService->get('addon-services');

        return view('admin.bookings.edit', [
            'booking' => $booking['data'],
            'roomTypes' => $roomTypes['success'] ? $roomTypes['data'] : [],
            'spaPackages' => $spaPackages['success'] ? $spaPackages['data'] : [],
            'spayPackages' => $spayPackages['success'] ? $spayPackages['data'] : [],
            'addonServices' => $addonServices['success'] ? $addonServices['data'] : [],
        ]);
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'special_requests' => 'sometimes|nullable|string|max:1000',
            'final_amount' => 'sometimes|numeric|min:0',
        ]);

        $response = $this->adminService->updateBooking($id, $validated);

        if ($response['success']) {
            return redirect()->route('admin.bookings.show', $id)
                ->with('success', 'Booking updated successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $response['message'] ?? 'Failed to update booking.');
    }

    /**
     * Confirm a booking
     */
    public function confirm($id, Request $request)
    {
        $response = $this->adminService->put("admin/bookings/{$id}/confirm", [
            'room_assignments' => $request->get('room_assignments', [])
        ]);

        if ($response['success']) {
            return redirect()->back()->with('success', 'Booking confirmed successfully.');
        }

        return redirect()->back()->with('error', $response['message'] ?? 'Failed to confirm booking.');
    }

    /**
     * Cancel a booking
     */
    public function cancel($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $response = $this->adminService->put("admin/bookings/{$id}/cancel", [
            'reason' => $request->reason
        ]);

        if ($response['success']) {
            return redirect()->back()->with('success', 'Booking cancelled successfully.');
        }

        return redirect()->back()->with('error', $response['message'] ?? 'Failed to cancel booking.');
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
}
