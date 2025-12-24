<?php
// app/Http/Controllers/Web/Community/AdoptionController.php
namespace App\Http\Controllers\Web\Community;

use App\Http\Controllers\Controller;
use App\Services\Web\CommunityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdoptionController extends Controller
{
    protected $communityService;

    public function __construct(CommunityService $communityService)
    {
        $this->communityService = $communityService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['age', 'gender', 'breed', 'location', 'search', 'page']);
        $params['per_page'] = 12;
        $response = $this->communityService->getAdoptions($params);

        return view('community.adoption', [
            'adoptions' => $response['success'] ? $response['data'] : [],
            'pagination' => $response['pagination'] ?? null,
            'filters' => $params
        ]);
    }

    public function show($id)
    {
        $response = $this->communityService->getAdoption($id);

        if (!$response['success']) {
            return redirect()->route('community.adoption')
                ->with('error', $response['message'] ?? 'Adoption listing not found.');
        }

        return view('community.adoption-detail', [
            'adoption' => $response['data']
        ]);
    }

    public function expressInterest(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('info', 'Please login to express interest in adoption.');
        }

        $request->validate([
            'message' => 'nullable|string|max:500'
        ]);

        $response = $this->communityService->expressInterest($id, $request->message);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Your interest has been registered! The owner will contact you soon.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to register interest.')
            ->withInput();
    }
}
