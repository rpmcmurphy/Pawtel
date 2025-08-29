<?php

namespace App\Http\Controllers\API\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $orders = $user->orders()
                ->with(['orderItems.product'])
                ->when($request->status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

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

    public function show(string $orderNumber): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = $user->orders()
                ->where('order_number', $orderNumber)
                ->with(['orderItems.product'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $cartItems = $user->cartItems()->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            DB::beginTransaction();

            // Check stock availability for all items
            foreach ($cartItems as $cartItem) {
                if ($cartItem->quantity > $cartItem->product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$cartItem->product->name}",
                        'product' => $cartItem->product->name,
                        'available_stock' => $cartItem->product->stock_quantity,
                        'requested_quantity' => $cartItem->quantity
                    ], 400);
                }
            }

            // Calculate order totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $deliveryCharge = 50.00; // Fixed delivery charge
            $totalAmount = $subtotal + $deliveryCharge;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'delivery_phone' => $request->delivery_phone,
                'delivery_notes' => $request->delivery_notes,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->price,
                    'total_price' => $cartItem->product->price * $cartItem->quantity,
                ]);

                // Decrease product stock
                $cartItem->product->decrementStock($cartItem->quantity);
            }

            // Clear cart
            $user->cartItems()->delete();

            // Send order confirmation
            $this->notificationService->sendOrderConfirmation($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => new OrderResource($order->load(['orderItems.product']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(string $orderNumber): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = $user->orders()
                ->where('order_number', $orderNumber)
                ->whereIn('status', ['pending', 'processing'])
                ->with('orderItems.product')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or cannot be cancelled'
                ], 404);
            }

            DB::beginTransaction();

            // Restore product stock
            foreach ($order->orderItems as $item) {
                $item->product->incrementStock($item->quantity);
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Send notification
            $this->notificationService->sendOrderStatusUpdate($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
