<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    // Spa Packages
    public function spaIndex()
    {
        $packages = $this->adminService->get('admin/services/spa-packages');
        return view('admin.services.spa.index', [
            'packages' => $packages['success'] ? ($packages['data']['data'] ?? $packages['data']) : []
        ]);
    }

    public function spaCreate()
    {
        return view('admin.services.spa.create');
    }

    public function spaStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'resident_price' => 'nullable|numeric|min:0',
            'max_daily_bookings' => 'required|integer|min:1',
        ]);

        $response = $this->adminService->post('admin/services/spa-packages', $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.services.spa.index')
                ->with('success', $apiResponse['message'] ?? 'Spa package created successfully.');
        }

        return redirect()->back()->withInput()->with('error', $apiResponse['message'] ?? 'Failed to create spa package.');
    }

    public function spaEdit($id)
    {
        $package = $this->adminService->get("admin/services/spa-packages/{$id}");
        if (!$package['success']) {
            return redirect()->route('admin.services.spa.index')->with('error', 'Package not found.');
        }
        return view('admin.services.spa.edit', [
            'package' => $package['data']['data'] ?? $package['data']
        ]);
    }

    public function spaUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'resident_price' => 'nullable|numeric|min:0',
            'max_daily_bookings' => 'required|integer|min:1',
        ]);

        $response = $this->adminService->put("admin/services/spa-packages/{$id}", $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.services.spa.index')
                ->with('success', $apiResponse['message'] ?? 'Spa package updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', $apiResponse['message'] ?? 'Failed to update spa package.');
    }

    // Spay Packages
    public function spayIndex()
    {
        $packages = $this->adminService->get('admin/services/spay-packages');
        return view('admin.services.spay.index', [
            'packages' => $packages['success'] ? ($packages['data']['data'] ?? $packages['data']) : []
        ]);
    }

    public function spayCreate()
    {
        return view('admin.services.spay.create');
    }

    public function spayStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:spay,neuter',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'resident_price' => 'nullable|numeric|min:0',
            'post_care_days' => 'required|integer|min:0',
            'post_care_rate_first_3_days' => 'nullable|numeric|min:0',
            'post_care_rate_next_4_days' => 'nullable|numeric|min:0',
            'post_care_rate_second_week' => 'nullable|numeric|min:0',
            'max_daily_slots' => 'required|integer|min:1',
        ]);

        $response = $this->adminService->post('admin/services/spay-packages', $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.services.spay.index')
                ->with('success', $apiResponse['message'] ?? 'Spay package created successfully.');
        }

        return redirect()->back()->withInput()->with('error', $apiResponse['message'] ?? 'Failed to create spay package.');
    }

    public function spayEdit($id)
    {
        $package = $this->adminService->get("admin/services/spay-packages/{$id}");
        if (!$package['success']) {
            return redirect()->route('admin.services.spay.index')->with('error', 'Package not found.');
        }
        return view('admin.services.spay.edit', [
            'package' => $package['data']['data'] ?? $package['data']
        ]);
    }

    public function spayUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:spay,neuter',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'resident_price' => 'nullable|numeric|min:0',
            'post_care_days' => 'required|integer|min:0',
            'post_care_rate_first_3_days' => 'nullable|numeric|min:0',
            'post_care_rate_next_4_days' => 'nullable|numeric|min:0',
            'post_care_rate_second_week' => 'nullable|numeric|min:0',
            'max_daily_slots' => 'required|integer|min:1',
        ]);

        $response = $this->adminService->put("admin/services/spay-packages/{$id}", $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.services.spay.index')
                ->with('success', $apiResponse['message'] ?? 'Spay package updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', $apiResponse['message'] ?? 'Failed to update spay package.');
    }

    // Addon Services
    public function addonIndex()
    {
        $services = $this->adminService->get('admin/services/addons');
        return view('admin.services.addons.index', [
            'services' => $services['success'] ? ($services['data']['data'] ?? $services['data']) : []
        ]);
    }

    public function addonCreate()
    {
        return view('admin.services.addons.create');
    }

    public function addonStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $response = $this->adminService->post('admin/services/addons', $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.services.addons.index')
                ->with('success', $apiResponse['message'] ?? 'Addon service created successfully.');
        }

        return redirect()->back()->withInput()->with('error', $apiResponse['message'] ?? 'Failed to create addon service.');
    }

    public function addonEdit($id)
    {
        $service = $this->adminService->get("admin/services/addons/{$id}");
        if (!$service['success']) {
            return redirect()->route('admin.services.addons.index')->with('error', 'Service not found.');
        }
        return view('admin.services.addons.edit', [
            'service' => $service['data']['data'] ?? $service['data']
        ]);
    }

    public function addonUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $response = $this->adminService->put("admin/services/addons/{$id}", $validated);
        $apiResponse = $response['success'] ? $response['data'] : $response;

        if (isset($apiResponse['success']) && $apiResponse['success']) {
            return redirect()->route('admin.services.addons.index')
                ->with('success', $apiResponse['message'] ?? 'Addon service updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', $apiResponse['message'] ?? 'Failed to update addon service.');
    }
}

