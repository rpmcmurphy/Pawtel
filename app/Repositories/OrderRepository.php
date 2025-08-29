<?php
// app/Repositories/OrderRepository.php
namespace App\Repositories;

use App\Models\Order;
use Carbon\Carbon;

class OrderRepository
{
    public function findOrFail(int $id): Order
    {
        return Order::findOrFail($id);
    }

    public function findWithItems(int $id): Order
    {
        return Order::with('orderItems.product')->findOrFail($id);
    }

    public function update(int $id, array $data): Order
    {
        $order = $this->findOrFail($id);
        $order->update($data);
        return $order->fresh();
    }

    public function count(): int
    {
        return Order::count();
    }

    public function countByStatus(string $status): int
    {
        return Order::where('status', $status)->count();
    }

    public function countForDate(Carbon $date): int
    {
        return Order::whereDate('created_at', $date)->count();
    }

    public function getRevenueForDate(Carbon $date): float
    {
        return Order::whereDate('created_at', $date)
            ->where('status', 'delivered')
            ->sum('total_amount');
    }

    public function getRevenueForPeriod(Carbon $start, Carbon $end): float
    {
        return Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'delivered')
            ->sum('total_amount');
    }

    public function getWithFilters(array $filters, int $perPage = 15)
    {
        $query = Order::with(['user', 'orderItems.product']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
