<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\Web\AuthService;
use App\Services\Web\BookingService;
use App\Services\Web\ShopService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $authService;
    protected $bookingService;
    protected $shopService;

    public function __construct(
        AuthService $authService,
        BookingService $bookingService,
        ShopService $shopService
    ) {
        $this->authService = $authService;
        $this->bookingService = $bookingService;
        $this->shopService = $shopService;
    }

    public function dashboard()
    {
        // Get user's recent bookings
        $recentBookings = $this->bookingService->getBookings(['limit' => 5]);

        // Get user's recent orders
        $recentOrders = $this->shopService->getOrders(['limit' => 5]);

        return view('my-account.dashboard', [
            'user' => $this->authService->getUser(),
            'recentBookings' => $recentBookings['success'] ? $recentBookings['data'] : [],
            'recentOrders' => $recentOrders['success'] ? $recentOrders['data'] : []
        ]);
    }

    public function show()
    {
        return view('auth.profile', [
            'user' => $this->authService->getUser()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:20',
        ]);

        $profileData = $request->only([
            'name',
            'phone',
            'address',
            'city',
            'emergency_contact'
        ]);

        $response = $this->authService->updateProfile($profileData);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Profile updated successfully!');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to update profile.');
    }

    public function bookings(Request $request)
    {
        $page = $request->get('page', 1);
        $status = $request->get('status', '');

        $params = ['page' => $page];
        if ($status) {
            $params['status'] = $status;
        }

        $bookings = $this->bookingService->getBookings($params);

        return view('my-account.bookings', [
            'bookings' => $bookings['success'] ? $bookings['data'] : [],
            'currentStatus' => $status
        ]);
    }

    public function orders(Request $request)
    {
        $page = $request->get('page', 1);
        $status = $request->get('status', '');

        $params = ['page' => $page];
        if ($status) {
            $params['status'] = $status;
        }

        $orders = $this->shopService->getOrders($params);

        return view('my-account.orders', [
            'orders' => $orders['success'] ? $orders['data'] : [],
            'currentStatus' => $status
        ]);
    }

    public function booking($id)
    {
        $booking = $this->bookingService->getBooking($id);

        if (!$booking['success']) {
            return redirect()->route('account.bookings')
                ->with('error', 'Booking not found.');
        }

        return view('my-account.booking-details', [
            'booking' => $booking['data']
        ]);
    }

    public function order($id)
    {
        $order = $this->shopService->getOrder($id);

        if (!$order['success']) {
            return redirect()->route('account.orders')
                ->with('error', 'Order not found.');
        }

        return view('my-account.order-details', [
            'order' => $order['data']
        ]);
    }

    public function cancelBooking(Request $request, $id)
    {
        $reason = $request->input('reason', 'Cancelled by customer');
        $response = $this->bookingService->cancelBooking($id, $reason);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Booking cancelled successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to cancel booking.');
    }
}
