<?php
// app/Repositories/ProductRepository.php
namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findOrFail(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findOrFail($id);
        $product->update($data);
        return $product->fresh();
    }

    public function delete(int $id): bool
    {
        return Product::destroy($id) > 0;
    }

    public function count(): int
    {
        return Product::count();
    }

    public function countActive(): int
    {
        return Product::where('status', 'active')->count();
    }

    public function countLowStock(): int
    {
        return Product::lowStock()->count();
    }

    public function countOutOfStock(): int
    {
        return Product::where('status', 'out_of_stock')->count();
    }

    // public function getTopSellingProducts(int $limit = 10): array
    // {
    //     return Product::withSum('orderItems as total_sold', 'quantity')
    //         ->orderBy('total_sold', 'desc')
    //         ->limit($limit)
    //         ->get()
    //         ->map(function ($product) {
    //             return [
    //                 'id' => $product->id,
    //                 'name' => $product->name,
    //                 'total_sold' => $product->total_sold ?? 0,
    //                 'price' => $product->price,
    //                 'revenue' => ($product->total_sold ?? 0) * $product->price,
    //             ];
    //         })
    //         ->toArray();
    // }

    public function getTopSellingProducts(int $limit = 10): array
    {
        return Product::select('products.id', 'products.name', 'products.price')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->selectRaw('COALESCE(SUM(order_items.total_price), 0) as total_revenue')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function ($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->whereIn('orders.status', ['delivered', 'completed']);
            })
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getWithFilters(array $filters, int $perPage = 15)
    {
        $query = Product::with(['category']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        if (!empty($filters['low_stock'])) {
            $query->lowStock();
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
