<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Web\ShopService;
use App\Services\Web\CommunityService;

class HomeController extends Controller
{
    protected $shopService;
    protected $communityService;

    public function __construct(ShopService $shopService, CommunityService $communityService)
    {
        $this->shopService = $shopService;
        $this->communityService = $communityService;
    }

    public function index()
    {
        // Get featured products for homepage
        $featuredProducts = $this->shopService->getFeaturedProducts();

        // Get recent community posts
        $recentPosts = $this->communityService->getPosts(['limit' => 6]);

        return view('home.index', [
            'featuredProducts' => $featuredProducts['success'] ? $featuredProducts['data'] : [],
            'recentPosts' => $recentPosts['success'] ? $recentPosts['data'] : []
        ]);
    }
}
