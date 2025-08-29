<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\{DashboardService, ReportService};
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private ReportService $reportService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getDashboardStats();
            $chartData = $this->dashboardService->getChartData();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'chart_data' => $chartData,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'bookings_by_type' => $this->reportService->getBookingsByType(),
                'bookings_by_status' => $this->reportService->getBookingsByStatus(),
                'monthly_revenue' => $this->reportService->getMonthlyRevenue(),
                'top_products' => $this->reportService->getTopProducts(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recentActivities(): JsonResponse
    {
        try {
            $activities = ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'action' => $activity->action,
                        'model_type' => $activity->model_type,
                        'user' => $activity->user ? $activity->user->name : 'System',
                        'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                        'time_ago' => $activity->created_at->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
