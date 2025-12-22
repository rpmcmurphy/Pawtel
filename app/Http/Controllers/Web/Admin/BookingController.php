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
        $spaPackages = $this->adminService->get('availability/spa-packages');
        $spayPackages = $this->adminService->get('availability/spay-packages');
        $addonServices = $this->adminService->get('admin/services/addons');

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
        
        // Add optional pricing override fields
        if (isset($validated['total_amount'])) {
            $bookingData['total_amount'] = $validated['total_amount'];
        }
        if (isset($validated['final_amount'])) {
            $bookingData['final_amount'] = $validated['final_amount'];
        }
        if (isset($validated['custom_monthly_discount'])) {
            $bookingData['custom_monthly_discount'] = $validated['custom_monthly_discount'];
        }
        
        // Add type-specific fields
        if ($validated['type'] === 'spa') {
            if (isset($validated['appointment_time'])) {
                $bookingData['appointment_time'] = $validated['appointment_time'];
            }
            if (isset($validated['notes'])) {
                $bookingData['notes'] = $validated['notes'];
            }
        }
        
        if ($validated['type'] === 'spay') {
            if (isset($validated['pet_name'])) {
                $bookingData['pet_name'] = $validated['pet_name'];
            }
            if (isset($validated['pet_age'])) {
                $bookingData['pet_age'] = $validated['pet_age'];
            }
            if (isset($validated['pet_weight'])) {
                $bookingData['pet_weight'] = $validated['pet_weight'];
            }
            if (isset($validated['medical_notes'])) {
                $bookingData['medical_notes'] = $validated['medical_notes'];
            }
            if (isset($validated['post_care_days'])) {
                $bookingData['post_care_days'] = $validated['post_care_days'];
            }
        }
        
        // Add status and confirmation flag
        $bookingData['status'] = $validated['status'] ?? 'confirmed';
        $bookingData['send_confirmation'] = $request->has('send_confirmation');

        // Create booking via API service
        $response = $this->adminService->createManualBooking($bookingData);

        Log::info('Manual Booking Creation Response:', $response);
        Log::info('Manual Booking Request Data:', $bookingData);

        // Handle nested response structure from ApiService
        $apiResponse = $response['success'] ? $response['data'] : $response;
        
        if (isset($apiResponse['success']) && $apiResponse['success']) {
            $message = $apiResponse['message'] ?? 'Booking created successfully.';
            
            // Get booking ID from nested structure
            $bookingId = $apiResponse['data']['id'] ?? null;
            
            if (!$bookingId) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Booking created but ID not found in response.');
            }

            // Send confirmation email if requested
            if ($request->has('send_confirmation')) {
                $message .= ' Confirmation email sent to customer.';
            }

            return redirect()->route('admin.bookings.show', $bookingId)
                ->with('success', $message);
        }

        // Better error handling
        $errorMessage = 'Failed to create booking.';

        if (isset($apiResponse['message'])) {
            $errorMessage = $apiResponse['message'];
        }

        if (isset($apiResponse['errors'])) {
            $errors = is_array($apiResponse['errors']) ? $apiResponse['errors'] : [$apiResponse['errors']];
            $errorMessage .= ' Errors: ' . implode(', ', Arr::flatten($errors));
        }
        
        if (isset($apiResponse['error'])) {
            $errorMessage = $apiResponse['error'];
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

        // Unwrap nested response structure
        $bookingData = $booking['data']['data'] ?? $booking['data'];

        return view('admin.bookings.show', [
            'booking' => $bookingData
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

        // Unwrap nested response structure
        $bookingData = $booking['success'] ? ($booking['data']['data'] ?? $booking['data']) : [];

        return view('admin.bookings.edit', [
            'booking' => $bookingData,
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

        $response = $this->adminService->put("admin/bookings/{$id}", $validated);
        
        // Handle nested response structure
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.bookings.show', $id)
                ->with('success', $apiResponse['message'] ?? 'Booking updated successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to update booking.');
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
     * Calculate booking price (proxy to API)
     */
    public function calculatePrice(Request $request)
    {
        // This is a proxy route that forwards to the API
        // The actual calculation is done in the API endpoint
        $response = $this->adminService->post('admin/bookings/calculate-price', $request->all());
        
        return response()->json($response);
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
