<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['status', 'search', 'date_from', 'date_to', 'page']);
        $orders = $this->adminService->getAdminOrders($params);

        return view('admin.orders.index', [
            'orders' => $orders['success'] ? ($orders['data']['data'] ?? $orders['data']) : [],
            'filters' => $params
        ]);
    }

    public function show($id)
    {
        $order = $this->adminService->getAdminOrder($id);

        if (!$order['success']) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Order not found.');
        }

        // Unwrap nested response structure
        $orderData = $order['data']['data'] ?? $order['data'];

        return view('admin.orders.show', [
            'order' => $orderData
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $response = $this->adminService->put("admin/orders/{$id}/status", [
            'status' => $request->status
        ]);

        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->back()
                ->with('success', $apiResponse['message'] ?? 'Order status updated successfully.');
        }

        return redirect()->back()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to update order status.');
    }

    public function ship($id)
    {
        $response = $this->adminService->post("admin/orders/{$id}/ship", []);

        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->back()
                ->with('success', $apiResponse['message'] ?? 'Order marked as shipped.');
        }

        return redirect()->back()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to mark order as shipped.');
    }

    public function deliver($id)
    {
        $response = $this->adminService->post("admin/orders/{$id}/deliver", []);

        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->back()
                ->with('success', $apiResponse['message'] ?? 'Order marked as delivered.');
        }

        return redirect()->back()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to mark order as delivered.');
    }

    public function cancel($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $response = $this->adminService->post("admin/orders/{$id}/cancel", [
            'reason' => $request->reason
        ]);

        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->back()
                ->with('success', $apiResponse['message'] ?? 'Order cancelled successfully.');
        }

        return redirect()->back()
            ->with('error', $apiResponse['message'] ?? $apiResponse['error'] ?? 'Failed to cancel order.');
    }

    public function invoice($id)
    {
        $order = $this->adminService->get("admin/orders/{$id}/invoice");

        if (!$order['success']) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Order invoice not found.');
        }

        // Return PDF or view
        $invoiceData = $order['data']['data'] ?? $order['data'];
        
        // For now, return view. Can be extended to generate PDF
        return view('admin.orders.invoice', [
            'order' => $invoiceData
        ]);
    }
}

