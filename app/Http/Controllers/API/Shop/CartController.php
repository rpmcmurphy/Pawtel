<?php

namespace App\Http\Controllers\API\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\AddToCartRequest;
use App\Http\Requests\Shop\UpdateCartRequest;
use App\Http\Resources\ProductResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $cartItems = $user->cartItems()
                ->with(['product.category'])
                ->get();

            $formattedItems = $cartItems->map(function ($cartItem) {
                $product = $cartItem->product;
                $product->cart_quantity = $cartItem->quantity;

                return new ProductResource($product);
            });

            $summary = [
                'items_count' => $cartItems->count(),
                'total_quantity' => $cartItems->sum('quantity'),
                'subtotal' => $cartItems->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                }),
                'estimated_delivery' => 50.00, // Fixed delivery charge for now
            ];

            $summary['total'] = $summary['subtotal'] + $summary['estimated_delivery'];

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $formattedItems,
                    'summary' => $summary,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $product = Product::active()->inStock()->find($request->product_id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found or out of stock'
                ], 404);
            }

            // Check if product is already in cart
            $existingCartItem = $user->cartItems()
                ->where('product_id', $product->id)
                ->first();

            DB::beginTransaction();

            if ($existingCartItem) {
                $newQuantity = $existingCartItem->quantity + $request->quantity;

                // Check stock availability
                if ($newQuantity > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock available',
                        'available_stock' => $product->stock_quantity,
                        'requested_quantity' => $newQuantity
                    ], 400);
                }

                $existingCartItem->update(['quantity' => $newQuantity]);
                $cartItem = $existingCartItem;
            } else {
                // Check stock availability
                if ($request->quantity > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock available',
                        'available_stock' => $product->stock_quantity,
                        'requested_quantity' => $request->quantity
                    ], 400);
                }

                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                ]);
            }

            DB::commit();

            // Load product data for response
            $product->cart_quantity = $cartItem->quantity;

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'data' => [
                    'cart_item' => new ProductResource($product->load('category')),
                    'cart_count' => $user->cartItems()->sum('quantity'),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(int $itemId, UpdateCartRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $cartItem = $user->cartItems()
                ->with('product')
                ->find($itemId);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            // Check stock availability
            if ($request->quantity > $cartItem->product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available',
                    'available_stock' => $cartItem->product->stock_quantity,
                    'requested_quantity' => $request->quantity
                ], 400);
            }

            $cartItem->update(['quantity' => $request->quantity]);

            // Load product data for response
            $product = $cartItem->product->load('category');
            $product->cart_quantity = $cartItem->quantity;

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => [
                    'cart_item' => new ProductResource($product),
                    'cart_count' => $user->cartItems()->sum('quantity'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function remove(int $itemId): JsonResponse
    {
        try {
            $user = Auth::user();
            $cartItem = $user->cartItems()->find($itemId);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully',
                'data' => [
                    'cart_count' => $user->cartItems()->sum('quantity'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->cartItems()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
