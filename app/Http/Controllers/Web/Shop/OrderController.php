<?php

namespace App\Http\Controllers\Web\Shop;

use App\Http\Controllers\Controller;
use App\Services\Web\ShopService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function checkout()
    {
        $cart = $this->shopService->getCart();

        if (!$cart['success'] || empty($cart['data']['items'])) {
            return redirect()->route('shop.cart')
                ->with('error', 'Your cart is empty.');
        }

        return view('shop.checkout', [
            'cart' => $cart['data']
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|string|in:cod,bank_transfer',
            'notes' => 'nullable|string|max:500',
        ]);

        $orderData = $request->only([
            'shipping_address',
            'shipping_city',
            'shipping_phone',
            'payment_method',
            'notes'
        ]);

        $response = $this->shopService->createOrder($orderData);

        if ($response['success']) {
            return redirect()->route('account.orders')
                ->with('success', 'Order placed successfully! We will contact you soon.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to place order.')
            ->withInput();
    }
}
