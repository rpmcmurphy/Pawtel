<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function bookings(Request $request)
    {
        $params = $request->only([
            'date_from',
            'date_to',
            'type',
            'status',
            'group_by',
            'format'
        ]);

        $reports = $this->adminService->getBookingReports($params);

        if ($request->get('format') === 'json') {
            return response()->json($reports);
        }

        return view('admin.reports.bookings', [
            'reports' => $reports['success'] ? $reports['data'] : [],
            'filters' => $params
        ]);
    }

    public function sales(Request $request)
    {
        $params = $request->only([
            'date_from',
            'date_to',
            'category',
            'product',
            'group_by',
            'format'
        ]);

        $reports = $this->adminService->getSalesReports($params);

        if ($request->get('format') === 'json') {
            return response()->json($reports);
        }

        return view('admin.reports.sales', [
            'reports' => $reports['success'] ? $reports['data'] : [],
            'filters' => $params
        ]);
    }

    // public function financial(Request $request)
    // {
    //     $params = $request->only([
    //         'date_from',
    //         'date_to',
    //         'type',
    //         'group_by',
    //         'format'
    //     ]);

    //     $reports = $this->adminService->getFinancialReports($params);

    //     if ($request->get('format') === 'json') {
    //         return response()->json($reports);
    //     }

    //     return view('admin.reports.financial', [
    //         'reports' => $reports['success'] ? $reports['data'] : [],
    //         'filters' => $params
    //     ]);
    // }

    public function financial(Request $request)
    {
        $params = $request->only([
            'date_from',
            'date_to',
            'type',
            'group_by',
            'format'
        ]);

        $reports = $this->adminService->getFinancialReports($params);

        if ($request->get('format') === 'json') {
            return response()->json($reports);
        }

        return view('admin.reports.financial', [
            'reports' => $reports['success'] ? $reports['data'] : [],
            'filters' => $params
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bookings,sales,financial',
            'format' => 'required|in:csv,excel,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $params = $request->only([
            'type',
            'format',
            'date_from',
            'date_to',
            'status',
            'category'
        ]);

        $response = $this->adminService->exportReport($params['type'], $params);

        if ($response['success']) {
            return response()->streamDownload(function () use ($response) {
                echo base64_decode($response['data']['file_content']);
            }, $response['data']['filename'], [
                'Content-Type' => $response['data']['mime_type']
            ]);
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to export report.');
    }
}
