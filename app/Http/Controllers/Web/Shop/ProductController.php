<?php

namespace App\Http\Controllers\Web\Shop;

use App\Http\Controllers\Controller;
use App\Services\Web\ShopService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['search', 'category', 'sort', 'page']);
        $products = $this->shopService->getProducts($params);
        $categories = $this->shopService->getCategories();

        return view('shop.index', [
            'products' => $products['success'] ? $products['data'] : [],
            'categories' => $categories['success'] ? $categories['data'] : [],
            'filters' => $params
        ]);
    }

    public function category(Request $request, $slug)
    {
        $params = $request->only(['search', 'sort', 'page']);
        $category = $this->shopService->getCategory($slug);
        $products = $this->shopService->getProductsByCategory($slug, $params);

        if (!$category['success']) {
            return redirect()->route('shop.index')->with('error', 'Category not found.');
        }

        return view('shop.category', [
            'category' => $category['data'],
            'products' => $products['success'] ? $products['data'] : [],
            'filters' => $params
        ]);
    }

    public function show($slug)
    {
        $product = $this->shopService->getProduct($slug);

        if (!$product['success']) {
            return redirect()->route('shop.index')->with('error', 'Product not found.');
        }

        // Get related products
        $relatedProducts = $this->shopService->getProducts(['limit' => 4, 'exclude' => $product['data']['data']['id']]);

        return view('shop.product', [
            'product' => $product['data']['data'],
            'relatedProducts' => $relatedProducts['success'] ? $relatedProducts['data'] : []
        ]);
    }
}
