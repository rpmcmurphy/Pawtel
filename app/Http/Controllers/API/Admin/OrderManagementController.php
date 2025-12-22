<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Services\Admin\OrderManagementService;
use App\Repositories\OrderRepository;
use Illuminate\Http\{JsonResponse, Request};

class OrderManagementController extends Controller
{
    public function __construct(
        private OrderManagementService $orderService,
        private OrderRepository $orderRepo
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderRepo->getWithFilters(
                $request->only(['status', 'date_from', 'date_to', 'search']),
                $request->get('per_page', 15)
            );

            return response()->json([
                'success' => true,
                'data' => OrderResource::collection($orders->items()),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepo->findWithItems($id);

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }

    public function updateStatus(int $id, UpdateOrderStatusRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->updateStatus($id, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function cancel(int $id, Request $request): JsonResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);

        try {
            $order = $this->orderService->cancelOrder($id, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function markAsShipped(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateStatus($id, 'shipped');

            return response()->json([
                'success' => true,
                'message' => 'Order marked as shipped successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function markAsDelivered(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateStatus($id, 'delivered');

            return response()->json([
                'success' => true,
                'message' => 'Order marked as delivered successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function invoice(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepo->findWithItems($id);

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }
}
