<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;

class DashboardController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        // Get dashboard statistics
        $stats = $this->adminService->getDashboardStats();
        $recentActivity = $this->adminService->getRecentActivities();

        return view('admin.dashboard', [
            'stats' => $stats['success'] ? $stats['data'] : [],
            'recentActivity' => $recentActivity['success'] ? $recentActivity['data'] : []
        ]);
    }

    public function stats()
    {
        $stats = $this->adminService->getDashboardStats();
        return response()->json($stats);
    }
}
