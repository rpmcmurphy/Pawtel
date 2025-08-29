<?php

namespace App\Http\Controllers\API\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category'])
                ->active()
                ->inStock();

            // Apply filters
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('sku', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->has('featured')) {
                $query->featured();
            }

            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');

            switch ($sortBy) {
                case 'price':
                    $query->orderBy('price', $sortOrder);
                    break;
                case 'name':
                    $query->orderBy('name', $sortOrder);
                    break;
                case 'created_at':
                    $query->orderBy('created_at', $sortOrder);
                    break;
                default:
                    $query->orderBy('sort_order', 'asc')
                        ->orderBy('featured', 'desc')
                        ->orderBy('created_at', 'desc');
            }

            $products = $query->paginate($request->get('per_page', 12));

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products->items()),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
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

    public function show(string $slug): JsonResponse
    {
        try {
            $product = Product::with(['category'])
                ->where('slug', $slug)
                ->active()
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function featured(Request $request): JsonResponse
    {
        try {
            $products = Product::with(['category'])
                ->active()
                ->inStock()
                ->featured()
                ->orderBy('sort_order', 'asc')
                ->limit($request->get('limit', 8))
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function byCategory(string $categorySlug, Request $request): JsonResponse
    {
        try {
            $category = ProductCategory::where('slug', $categorySlug)
                ->active()
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $query = Product::with(['category'])
                ->active()
                ->inStock()
                ->where('category_id', $category->id);

            // Apply additional filters
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');

            switch ($sortBy) {
                case 'price':
                    $query->orderBy('price', $sortOrder);
                    break;
                case 'name':
                    $query->orderBy('name', $sortOrder);
                    break;
                default:
                    $query->orderBy('sort_order', 'asc')
                        ->orderBy('featured', 'desc');
            }

            $products = $query->paginate($request->get('per_page', 12));

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'full_path' => $category->getFullPath(),
                    ],
                    'products' => ProductResource::collection($products->items()),
                ],
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
