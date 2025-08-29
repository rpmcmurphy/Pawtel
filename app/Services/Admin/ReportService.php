<?php
// app/Services/Admin/ReportService.php
namespace App\Services\Admin;

use App\Repositories\{BookingRepository, OrderRepository, ProductRepository};
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        private BookingRepository $bookingRepo,
        private OrderRepository $orderRepo,
        private ProductRepository $productRepo
    ) {}

    public function getMonthlyRevenue(int $months = 12): array
    {
        return collect(range($months - 1, 0))->map(function ($months) {
            $date = Carbon::now()->subMonths($months);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $bookingRevenue = $this->bookingRepo->getRevenueForPeriod($start, $end);
            $orderRevenue = $this->orderRepo->getRevenueForPeriod($start, $end);

            return [
                'month' => $date->format('M Y'),
                'bookings' => $bookingRevenue,
                'orders' => $orderRevenue,
                'total' => $bookingRevenue + $orderRevenue,
            ];
        })->toArray();
    }

    public function getTopProducts(int $limit = 10): array
    {
        return $this->productRepo->getTopSellingProducts($limit);
    }

    public function getBookingsByType(): array
    {
        return $this->bookingRepo->getCountByType();
    }

    public function getBookingsByStatus(): array
    {
        return $this->bookingRepo->getCountByStatus();
    }
}
