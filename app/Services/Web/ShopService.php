<?php

namespace App\Services\Web;

class ShopService extends ApiService
{
    // Category Methods
    public function getCategories()
    {
        return $this->get('shop/categories');
    }

    public function getCategory($slug)
    {
        return $this->get("shop/categories/{$slug}");
    }

    // Product Methods
    public function getProducts($params = [])
    {
        return $this->get('shop/products', $params);
    }

    public function getProduct($slug)
    {
        return $this->get("shop/products/{$slug}");
    }

    public function getProductsByCategory($categorySlug, $params = [])
    {
        return $this->get("shop/products/category/{$categorySlug}", $params);
    }

    public function getFeaturedProducts()
    {
        return $this->get('shop/featured-products');
    }

    public function searchProducts($query, $params = [])
    {
        $params['search'] = $query;
        return $this->get('shop/products', $params);
    }

    // Cart Methods
    public function getCart()
    {
        return $this->get('cart');
    }

    public function addToCart($productId, $quantity = 1)
    {
        return $this->post('cart/add', [
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }

    public function updateCartItem($cartItemId, $quantity)
    {
        return $this->put("cart/{$cartItemId}", ['quantity' => $quantity]);
    }

    public function removeFromCart($cartItemId)
    {
        return $this->delete("cart/{$cartItemId}");
    }

    public function clearCart()
    {
        return $this->delete('cart/clear');
    }

    // Order Methods
    public function createOrder($orderData)
    {
        return $this->post('orders', $orderData);
    }

    public function getOrders($params = [])
    {
        return $this->get('orders', $params);
    }

    public function getOrder($id)
    {
        return $this->get("orders/{$id}");
    }
}
