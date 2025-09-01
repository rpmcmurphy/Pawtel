<?php
namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\ProductRepository;
use App\Services\Admin\ProductManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductManagementController extends Controller
{
    public function __construct(
        private ProductRepository $productRepo,
        private ProductManagementService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'category_id', 'featured', 'search', 'low_stock']);
            $products = $this->productRepo->getWithFilters($filters, $request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products->items()),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        Log::error('Product Dataaaa:', $request->validated());
        try {
            $product = $this->productService->createProduct($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => new ProductResource($product->load('category'))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productRepo->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product->load('category'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(int $id, UpdateProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductResource($product->load('category'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function toggleFeatured(int $id): JsonResponse
    {
        try {
            $product = $this->productService->toggleFeatured($id);

            return response()->json([
                'success' => true,
                'message' => $product->featured ? 'Product marked as featured' : 'Product unmarked as featured',
                'data' => ['featured' => $product->featured]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function updateStock(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'action' => 'required|in:set,add,subtract',
        ]);

        try {
            $oldProduct = $this->productRepo->findOrFail($id);
            $newProduct = $this->productService->updateStock($id, $request->stock_quantity, $request->action);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'data' => [
                    'old_stock' => $oldProduct->stock_quantity,
                    'new_stock' => $newProduct->stock_quantity,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive,out_of_stock'
        ]);

        try {
            $product = $this->productRepo->update($id, [
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully',
                'data' => [
                    'status' => $product->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function categoryTree(): JsonResponse
    {
        try {
            $categories = ProductCategory::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'products_count' => $category->products()->count(),
                        'children' => $category->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                                'products_count' => $child->products()->count(),
                            ];
                        }),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category tree',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_products' => $this->productRepo->count(),
                'active_products' => $this->productRepo->countActive(),
                'low_stock_products' => $this->productRepo->countLowStock(),
                'out_of_stock_products' => $this->productRepo->countOutOfStock(),
                'featured_products' => Product::where('featured', true)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function topSelling(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $products = $this->productRepo->getTopSellingProducts($limit);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top selling products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}