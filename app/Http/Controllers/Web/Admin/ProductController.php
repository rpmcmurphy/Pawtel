<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use App\Services\Web\ShopService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $adminService;
    protected $shopService;

    public function __construct(AdminService $adminService, ShopService $shopService)
    {
        $this->adminService = $adminService;
        $this->shopService = $shopService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['status', 'category_id', 'featured', 'search', 'page']);
        $products = $this->adminService->getAdminProducts($params);
        $categories = $this->shopService->getCategories();

        return view('admin.products.index', [
            'products' => $products['success'] ? ($products['data'] ?? []) : [],
            'categories' => $categories['success'] ? ($categories['data']['data'] ?? []) : [],
            'filters' => $params
        ]);
    }

    public function create()
    {
        $categories = $this->shopService->getCategories();

        return view('admin.products.create', [
            'categories' => $categories['success'] ? ($categories['data']['data'] ?? []) : []
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|integer|exists:product_categories,id',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,out_of_stock',
            'featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'required_with:specifications.*.value|string|max:255',
            'specifications.*.value' => 'required_with:specifications.*.key|string|max:255',
        ]);

        $productData = $request->only([
            'name',
            'description',
            'price',
            'compare_price',
            'category_id',
            'sku',
            'stock_quantity',
            'low_stock_threshold',
            'status'
        ]);

        // Handle featured checkbox
        $productData['featured'] = $request->has('featured');

        // Handle specifications - filter out empty ones
        $specifications = [];
        if ($request->has('specifications')) {
            foreach ($request->input('specifications', []) as $spec) {
                if (!empty($spec['key']) && !empty($spec['value'])) {
                    $specifications[$spec['key']] = $spec['value'];
                }
            }
        }
        $productData['specifications'] = $specifications;

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uploadResponse = $this->adminService->uploadProductImage($image);
                if ($uploadResponse['success']) {
                    $images[] = $uploadResponse['data']['path'];
                }
            }
        }
        $productData['images'] = $images;

        // Set default values
        $productData['low_stock_threshold'] = $productData['low_stock_threshold'] ?? 5;

        $response = $this->adminService->createProduct($productData);

        if ($response['success']) {
            $message = 'Product created successfully!';

            if ($request->has('save_and_continue')) {
                return redirect()->route('admin.products.edit', $response['data']['id'])
                    ->with('success', $message);
            }

            return redirect()->route('admin.products.index')
                ->with('success', $message);
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to create product.')
            ->withInput();
    }

    public function show($id)
    {
        $product = $this->adminService->getAdminProduct($id);

        if (!$product['success']) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Product not found.');
        }

        return view('admin.products.show', [
            'product' => $product['data']['data'] ?? $product['data']
        ]);
    }

    public function edit($id)
    {
        $product = $this->adminService->getAdminProduct($id);
        $categories = $this->shopService->getCategories();

        if (!$product['success']) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Product not found.');
        }

        return view('admin.products.edit', [
            'product' => $product['data']['data'] ?? $product['data'],
            'categories' => $categories['success'] ? ($categories['data']['data'] ?? []) : []
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|integer|exists:product_categories,id',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $id,
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,out_of_stock',
            'featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'required_with:specifications.*.value|string|max:255',
            'specifications.*.value' => 'required_with:specifications.*.key|string|max:255',
        ]);

        $productData = $request->only([
            'name',
            'description',
            'price',
            'compare_price',
            'category_id',
            'sku',
            'stock_quantity',
            'low_stock_threshold',
            'status'
        ]);

        // Handle featured checkbox
        $productData['featured'] = $request->has('featured');

        // Handle specifications - filter out empty ones
        $specifications = [];
        if ($request->has('specifications')) {
            foreach ($request->input('specifications', []) as $spec) {
                if (!empty($spec['key']) && !empty($spec['value'])) {
                    $specifications[$spec['key']] = $spec['value'];
                }
            }
        }
        $productData['specifications'] = $specifications;

        // Handle image uploads - only add if new images are uploaded
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $uploadResponse = $this->adminService->uploadProductImage($image);
                if ($uploadResponse['success']) {
                    $images[] = $uploadResponse['data']['path'];
                }
            }
            // Only update images if new ones were uploaded
            if (!empty($images)) {
                $productData['images'] = $images;
            }
        }

        $response = $this->adminService->updateProduct($id, $productData);

        if ($response['success']) {
            $message = 'Product updated successfully!';

            if ($request->has('save_and_continue')) {
                return redirect()->route('admin.products.edit', $id)
                    ->with('success', $message);
            }

            return redirect()->route('admin.products.index')
                ->with('success', $message);
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to update product.')
            ->withInput();
    }

    public function destroy($id)
    {
        $response = $this->adminService->deleteProduct($id);

        if ($response['success']) {
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully!');
        }

        return redirect()->route('admin.products.index')
            ->with('error', $response['message'] ?? 'Failed to delete product.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,out_of_stock'
        ]);

        $response = $this->adminService->updateProductStatus($id, $request->status);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to update product status.'
        ], 400);
    }
}
