<?php
// app/Services/Admin/OrderManagementService.php
namespace App\Services\Admin;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class OrderManagementService
{
    private array $validTransitions = [
        'pending' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function __construct(
        private OrderRepository $orderRepo,
        private NotificationService $notificationService
    ) {}

    public function updateStatus(int $orderId, string $newStatus): Order
    {
        $order = $this->orderRepo->findOrFail($orderId);
        $oldStatus = $order->status;

        if (!in_array($newStatus, $this->validTransitions[$oldStatus])) {
            throw new \Exception("Cannot change status from {$oldStatus} to {$newStatus}");
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'delivered') {
            $updateData['delivered_at'] = now();
        } elseif ($newStatus === 'cancelled') {
            $updateData['cancelled_at'] = now();
        }

        $order = $this->orderRepo->update($orderId, $updateData);
        $this->notificationService->sendOrderStatusUpdate($order);

        return $order;
    }

    public function cancelOrder(int $orderId, ?string $reason = null): Order
    {
        $order = $this->orderRepo->findWithItems($orderId);

        if (!in_array($order->status, ['pending', 'processing'])) {
            throw new \Exception('This order cannot be cancelled');
        }

        DB::beginTransaction();
        try {
            // Restore product stock
            foreach ($order->orderItems as $item) {
                $item->product->incrementStock($item->quantity);
            }

            $order = $this->orderRepo->update($orderId, [
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $this->notificationService->sendOrderStatusUpdate($order);

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
