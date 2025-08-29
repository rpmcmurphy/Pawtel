<?php

namespace App\Http\Controllers\API\Shop;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $categories = ProductCategory::active()
                ->with(['children' => function ($query) {
                    $query->active()->orderBy('sort_order');
                }])
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'image' => $category->image,
                        'products_count' => $category->products()->active()->count(),
                        'children' => $category->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                                'products_count' => $child->products()->active()->count(),
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
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $category = ProductCategory::with(['parent', 'children'])
                ->where('slug', $slug)
                ->active()
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->image,
                    'full_path' => $category->getFullPath(),
                    'products_count' => $category->products()->active()->count(),
                    'parent' => $category->parent ? [
                        'id' => $category->parent->id,
                        'name' => $category->parent->name,
                        'slug' => $category->parent->slug,
                    ] : null,
                    'children' => $category->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'slug' => $child->slug,
                            'products_count' => $child->products()->active()->count(),
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
