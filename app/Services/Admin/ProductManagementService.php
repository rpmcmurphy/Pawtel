<?php
// app/Services/Admin/ProductManagementService.php
namespace App\Services\Admin;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;

class ProductManagementService
{
    public function __construct(
        private ProductRepository $productRepo
    ) {}

    public function createProduct(array $data): Product
    {
        $data['slug'] = Str::slug($data['name']);
        $data['sku'] = strtoupper($data['sku']);
        $data['status'] = 'active';
        $data['specifications'] = $data['specifications'] ?? [];

        return $this->productRepo->create($data);
    }

    public function updateProduct(int $id, array $data): Product
    {
        $data['slug'] = Str::slug($data['name']);
        $data['sku'] = strtoupper($data['sku']);
        $data['specifications'] = $data['specifications'] ?? [];

        return $this->productRepo->update($id, $data);
    }

    public function toggleFeatured(int $id): Product
    {
        $product = $this->productRepo->findOrFail($id);
        return $this->productRepo->update($id, [
            'featured' => !$product->featured
        ]);
    }

    public function updateStock(int $id, int $quantity, string $action): Product
    {
        $product = $this->productRepo->findOrFail($id);
        $newStock = $product->stock_quantity;

        switch ($action) {
            case 'set':
                $newStock = $quantity;
                break;
            case 'add':
                $newStock += $quantity;
                break;
            case 'subtract':
                $newStock = max(0, $newStock - $quantity);
                break;
        }

        return $this->productRepo->update($id, ['stock_quantity' => $newStock]);
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepo->findOrFail($id);

        if ($product->orderItems()->exists()) {
            throw new \Exception('Cannot delete product with existing orders');
        }

        return $this->productRepo->delete($id);
    }
}
