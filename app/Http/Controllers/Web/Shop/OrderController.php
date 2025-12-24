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

        if (!$cart['success']) {
            return redirect()->route('shop.cart')
                ->with('error', 'Your cart is empty.');
        }

        // Transform API response to match view expectations
        $apiData = $cart['data']['data'] ?? $cart['data'];
        $items = $apiData['items'] ?? [];
        $summary = $apiData['summary'] ?? [];

        if (empty($items)) {
            return redirect()->route('shop.cart')
                ->with('error', 'Your cart is empty.');
        }

        // Transform items to match view structure
        $transformedItems = [];
        foreach ($items as $item) {
            $product = $item['data'] ?? $item;
            $transformedItems[] = [
                'id' => $product['id'] ?? null,
                'quantity' => $product['cart_quantity'] ?? $product['quantity'] ?? 1,
                'unit_price' => $product['price'] ?? 0,
                'total_price' => ($product['price'] ?? 0) * ($product['cart_quantity'] ?? $product['quantity'] ?? 1),
                'product' => [
                    'id' => $product['id'] ?? null,
                    'name' => $product['name'] ?? '',
                    'image_url' => $product['image_url'] ?? '',
                    'brand' => $product['brand'] ?? '',
                ]
            ];
        }

        $cartData = [
            'items' => $transformedItems,
            'subtotal' => $summary['subtotal'] ?? 0,
            'discount_amount' => $summary['discount'] ?? 0,
            'shipping_cost' => $summary['estimated_delivery'] ?? 0,
            'total' => $summary['total'] ?? ($summary['subtotal'] ?? 0) + ($summary['estimated_delivery'] ?? 0),
        ];

        return view('shop.checkout', [
            'cart' => $cartData
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
