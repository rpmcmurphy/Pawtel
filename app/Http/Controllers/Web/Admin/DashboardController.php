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

        // dd($stats);
        // array:3 [▼ // app\Http\Controllers\Web\Admin\DashboardController.php:23
        // "success" => true
        // "data" => array:2 [▼
        //     "success" => true
        //     "data" => array:4 [▼
        //     "bookings_by_type" => array:3 [▼
        //         0 => array:2 [▼
        //         "type" => "hotel"
        //         "count" => 3
        //         ]
        //         1 => array:2 [▶]
        //         2 => array:2 [▶]
        //     ]
        //     "bookings_by_status" => array:2 [▼
        //         0 => array:2 [▼
        //         "status" => "pending"
        //         "count" => 3
        //         ]
        //         1 => array:2 [▶]
        //     ]
        //     "monthly_revenue" => array:12 [▼
        //         0 => array:4 [▼
        //         "month" => "Jan 2025"
        //         "bookings" => 0
        //         "orders" => 0
        //         "total" => 0
        //         ]
        //         1 => array:4 [▶]
        //         2 => array:4 [▶]
        //         3 => array:4 [▶]
        //         4 => array:4 [▶]
        //         5 => array:4 [▶]
        //         6 => array:4 [▶]
        //         7 => array:4 [▶]
        //         8 => array:4 [▶]
        //         9 => array:4 [▶]
        //         10 => array:4 [▶]
        //         11 => array:4 [▶]
        //     ]
        //     "top_products" => array:7 [▼
        //         0 => array:5 [▼
        //         "id" => 1
        //         "name" => "Royal Canin Adult Cat Food"
        //         "price" => "2500.00"
        //         "total_sold" => "2"
        //         "total_revenue" => "5000.00"
        //         ]
        //         1 => array:5 [▶]
        //         2 => array:5 [▶]
        //         3 => array:5 [▶]
        //         4 => array:5 [▶]
        //         5 => array:5 [▶]
        //         6 => array:5 [▶]
        //     ]
        //     ]
        // ]
        // "status" => 200
        // ]
        // dd($recentActivity);
        // array:3 [▼ // app\Http\Controllers\Web\Admin\DashboardController.php:82
        // "success" => true
        // "data" => array:2 [▼
        //     "success" => true
        //     "data" => []
        // ]
        // "status" => 200
        // ]

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
