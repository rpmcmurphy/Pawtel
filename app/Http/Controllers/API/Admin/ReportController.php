<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportService;
use App\Repositories\{BookingRepository, OrderRepository, ProductRepository, UserRepository};
use Illuminate\Http\{JsonResponse, Request};
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private BookingRepository $bookingRepo,
        private OrderRepository $orderRepo,
        private ProductRepository $productRepo,
        private UserRepository $userRepo
    ) {}

    public function bookings(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'type', 'status', 'group_by']);

            $dateFrom = $filters['date_from'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');
            $groupBy = $filters['group_by'] ?? 'day';

            $data = [];

            // Basic stats
            $data['stats'] = [
                'total_bookings' => $this->bookingRepo->count(),
                'confirmed_bookings' => $this->bookingRepo->countByStatus('confirmed'),
                'pending_bookings' => $this->bookingRepo->countByStatus('pending'),
                'cancelled_bookings' => $this->bookingRepo->countByStatus('cancelled'),
                'revenue' => $this->bookingRepo->getRevenueForPeriod(
                    Carbon::parse($dateFrom),
                    Carbon::parse($dateTo)
                )
            ];

            // Bookings by type
            $data['by_type'] = $this->reportService->getBookingsByType();

            // Bookings by status
            $data['by_status'] = $this->reportService->getBookingsByStatus();

            // Revenue trend
            $data['revenue_trend'] = $this->getRevenueTrend($dateFrom, $dateTo, $groupBy, 'bookings');

            return response()->json([
                'success' => true,
                'data' => $data,
                'filters' => array_merge(['date_from' => $dateFrom, 'date_to' => $dateTo], $filters)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch booking reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sales(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'category', 'product', 'group_by']);

            $dateFrom = $filters['date_from'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');
            $groupBy = $filters['group_by'] ?? 'day';

            $data = [];

            // Basic stats
            $data['stats'] = [
                'total_orders' => $this->orderRepo->count(),
                'completed_orders' => $this->orderRepo->countByStatus('delivered'),
                'pending_orders' => $this->orderRepo->countByStatus('pending'),
                'cancelled_orders' => $this->orderRepo->countByStatus('cancelled'),
                'revenue' => $this->orderRepo->getRevenueForPeriod(
                    Carbon::parse($dateFrom),
                    Carbon::parse($dateTo)
                )
            ];

            // Top products
            $data['top_products'] = $this->reportService->getTopProducts(10);

            // Revenue trend
            $data['revenue_trend'] = $this->getRevenueTrend($dateFrom, $dateTo, $groupBy, 'sales');

            // Orders by status
            $data['by_status'] = $this->orderRepo->getCountByStatus();

            return response()->json([
                'success' => true,
                'data' => $data,
                'filters' => array_merge(['date_from' => $dateFrom, 'date_to' => $dateTo], $filters)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sales reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function financial(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'type', 'group_by']);

            $dateFrom = $filters['date_from'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');
            $groupBy = $filters['group_by'] ?? 'day';

            $data = [];

            // Basic stats
            $bookingRevenue = $this->bookingRepo->getRevenueForPeriod(
                Carbon::parse($dateFrom),
                Carbon::parse($dateTo)
            );
            $salesRevenue = $this->orderRepo->getRevenueForPeriod(
                Carbon::parse($dateFrom),
                Carbon::parse($dateTo)
            );

            $data['stats'] = [
                'total_revenue' => $bookingRevenue + $salesRevenue,
                'booking_revenue' => $bookingRevenue,
                'sales_revenue' => $salesRevenue,
                'avg_booking_value' => $this->getAverageBookingValue($dateFrom, $dateTo),
                'avg_order_value' => $this->getAverageOrderValue($dateFrom, $dateTo)
            ];

            // Monthly revenue breakdown
            $data['monthly_revenue'] = $this->reportService->getMonthlyRevenue(12);

            // Revenue by source
            $data['revenue_by_source'] = [
                ['source' => 'Bookings', 'amount' => $bookingRevenue],
                ['source' => 'Sales', 'amount' => $salesRevenue]
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'filters' => array_merge(['date_from' => $dateFrom, 'date_to' => $dateTo], $filters)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch financial reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportBookings(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'csv');
            $filters = $request->only(['date_from', 'date_to', 'status', 'type']);

            // This would typically generate and return a file
            // For now, return success with mock data
            return response()->json([
                'success' => true,
                'data' => [
                    'filename' => "bookings_report_" . date('Y-m-d') . ".$format",
                    'mime_type' => $format === 'pdf' ? 'application/pdf' : 'text/csv',
                    'file_content' => base64_encode('Mock export data')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export booking report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportSales(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'csv');
            $filters = $request->only(['date_from', 'date_to', 'status']);

            return response()->json([
                'success' => true,
                'data' => [
                    'filename' => "sales_report_" . date('Y-m-d') . ".$format",
                    'mime_type' => $format === 'pdf' ? 'application/pdf' : 'text/csv',
                    'file_content' => base64_encode('Mock export data')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export sales report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getRevenueTrend(string $dateFrom, string $dateTo, string $groupBy, string $type): array
    {
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        $data = [];

        if ($groupBy === 'day') {
            $current = $start->copy();
            while ($current->lte($end)) {
                $revenue = $type === 'bookings'
                    ? $this->bookingRepo->getRevenueForDate($current)
                    : $this->orderRepo->getRevenueForDate($current);

                $data[] = [
                    'date' => $current->format('Y-m-d'),
                    'revenue' => $revenue
                ];
                $current->addDay();
            }
        }

        return $data;
    }

    private function getAverageBookingValue(string $dateFrom, string $dateTo): float
    {
        $revenue = $this->bookingRepo->getRevenueForPeriod(
            Carbon::parse($dateFrom),
            Carbon::parse($dateTo)
        );
        $count = $this->bookingRepo->count();

        return $count > 0 ? $revenue / $count : 0;
    }

    private function getAverageOrderValue(string $dateFrom, string $dateTo): float
    {
        $revenue = $this->orderRepo->getRevenueForPeriod(
            Carbon::parse($dateFrom),
            Carbon::parse($dateTo)
        );
        $count = $this->orderRepo->count();

        return $count > 0 ? $revenue / $count : 0;
    }
}