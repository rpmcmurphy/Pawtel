<?php

namespace App\Http\Controllers\Web\Shop;

use App\Http\Controllers\Controller;
use App\Services\Web\ShopService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index()
    {
        $cart = $this->shopService->getCart();

        return view('shop.cart', [
            'cart' => $cart['success'] ? $cart['data'] : []
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $response = $this->shopService->addToCart($request->product_id, $request->quantity);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $response['data']['cart_count'] ?? 0
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to add product to cart.'
        ], 400);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $response = $this->shopService->updateCartItem($id, $request->quantity);

        if ($response['success']) {
            return redirect()->back()->with('success', 'Cart updated successfully!');
        }

        return redirect()->back()->with('error', $response['message'] ?? 'Failed to update cart.');
    }

    public function remove($id)
    {
        $response = $this->shopService->removeFromCart($id);

        if ($response['success']) {
            return redirect()->back()->with('success', 'Item removed from cart.');
        }

        return redirect()->back()->with('error', $response['message'] ?? 'Failed to remove item.');
    }
}
