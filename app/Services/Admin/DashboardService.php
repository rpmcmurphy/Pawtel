<?php
// app/Services/Admin/DashboardService.php
namespace App\Services\Admin;

use App\Models\{Booking, Order, User, Product};
use App\Repositories\{BookingRepository, OrderRepository, UserRepository, ProductRepository};
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        private BookingRepository $bookingRepo,
        private OrderRepository $orderRepo,
        private UserRepository $userRepo,
        private ProductRepository $productRepo
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'bookings' => $this->getBookingStats(),
            'orders' => $this->getOrderStats(),
            'users' => $this->getUserStats(),
            'products' => $this->getProductStats(),
            'revenue' => $this->getRevenueStats(),
        ];
    }

    public function getChartData(): array
    {
        return collect(range(6, 0))->map(function ($days) {
            $date = Carbon::now()->subDays($days);
            return [
                'date' => $date->format('M d'),
                'bookings' => $this->bookingRepo->getRevenueForDate($date),
                'orders' => $this->orderRepo->getRevenueForDate($date),
                'total' => $this->bookingRepo->getRevenueForDate($date) + $this->orderRepo->getRevenueForDate($date),
            ];
        })->toArray();
    }

    private function getBookingStats(): array
    {
        return [
            'total' => $this->bookingRepo->count(),
            'pending' => $this->bookingRepo->countByStatus('pending'),
            'confirmed' => $this->bookingRepo->countByStatus('confirmed'),
            'today' => $this->bookingRepo->countForDate(today()),
        ];
    }

    private function getOrderStats(): array
    {
        return [
            'total' => $this->orderRepo->count(),
            'pending' => $this->orderRepo->countByStatus('pending'),
            'delivered' => $this->orderRepo->countByStatus('delivered'),
            'today' => $this->orderRepo->countForDate(today()),
        ];
    }

    private function getUserStats(): array
    {
        return [
            'total' => $this->userRepo->countCustomers(),
            'active' => $this->userRepo->countActiveCustomers(),
            'new_today' => $this->userRepo->countNewCustomersToday(),
        ];
    }

    private function getProductStats(): array
    {
        return [
            'total' => $this->productRepo->count(),
            'active' => $this->productRepo->countActive(),
            'low_stock' => $this->productRepo->countLowStock(),
            'out_of_stock' => $this->productRepo->countOutOfStock(),
        ];
    }

    private function getRevenueStats(): array
    {
        return [
            'today' => $this->getTodayRevenue(),
            'this_month' => $this->getThisMonthRevenue(),
            'last_month' => $this->getLastMonthRevenue(),
        ];
    }

    private function getTodayRevenue(): float
    {
        return $this->bookingRepo->getRevenueForDate(today()) +
            $this->orderRepo->getRevenueForDate(today());
    }

    private function getThisMonthRevenue(): float
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        return $this->bookingRepo->getRevenueForPeriod($start, $end) +
            $this->orderRepo->getRevenueForPeriod($start, $end);
    }

    private function getLastMonthRevenue(): float
    {
        $start = Carbon::now()->subMonth()->startOfMonth();
        $end = Carbon::now()->subMonth()->endOfMonth();
        return $this->bookingRepo->getRevenueForPeriod($start, $end) +
            $this->orderRepo->getRevenueForPeriod($start, $end);
    }
}
