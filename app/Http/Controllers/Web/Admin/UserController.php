<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['role', 'status', 'verified', 'search', 'page']);
        $users = $this->adminService->getAdminUsers($params);

        return view('admin.users.index', [
            'users' => $users['success'] ? $users['data'] : [],
            'filters' => $params
        ]);
    }

    public function show($id)
    {
        $user = $this->adminService->getAdminUser($id);

        if (!$user['success']) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }

        return view('admin.users.show', [
            'user' => $user['data']
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,banned'
        ]);

        $response = $this->adminService->updateUserStatus($id, $request->status);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'User status updated successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to update user status.');
    }

    public function bookings($id, Request $request)
    {
        $params = $request->only(['status', 'type', 'page']);
        $bookings = $this->adminService->getUserBookings($id, $params);

        return view('admin.users.bookings', [
            'user_id' => $id,
            'bookings' => $bookings['success'] ? $bookings['data'] : [],
            'filters' => $params
        ]);
    }

    public function orders($id, Request $request)
    {
        $params = $request->only(['status', 'page']);
        $orders = $this->adminService->getUserOrders($id, $params);

        return view('admin.users.orders', [
            'user_id' => $id,
            'orders' => $orders['success'] ? $orders['data'] : [],
            'filters' => $params
        ]);
    }
}
