<?php

namespace App\Http\Controllers\Web\Community;

use App\Http\Controllers\Controller;
use App\Services\Web\CommunityService;
use App\Services\Web\AuthService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $communityService;
    protected $authService;

    public function __construct(CommunityService $communityService, AuthService $authService)
    {
        $this->communityService = $communityService;
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['category', 'search', 'sort', 'page']);
        $params['per_page'] = 10;
        $response = $this->communityService->getPosts($params);

        return view('community.posts', [
            'posts' => $response['success'] ? $response['data'] : [],
            'pagination' => $response['pagination'] ?? null,
            'filters' => $params
        ]);
    }

    public function show($slug)
    {
        $response = $this->communityService->getPost($slug);

        if (!$response['success']) {
            return redirect()->route('community.posts')
                ->with('error', $response['message'] ?? 'Post not found.');
        }

        $post = $response['data'];
        
        // Comments are included in the post data from API
        $comments = $post['recent_comments'] ?? [];

        return view('community.post', [
            'post' => $post,
            'comments' => $comments
        ]);
    }

    public function like(Request $request, $id)
    {
        if (!$this->authService->isAuthenticated()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to like posts.'
            ], 401);
        }

        // Check if user wants to like or unlike based on current state
        $isLiked = $request->input('is_liked', false);
        
        if ($isLiked) {
            $response = $this->communityService->unlikePost($id);
        } else {
            $response = $this->communityService->likePost($id);
        }

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => $response['message'] ?? 'Success',
                'data' => $response['data'] ?? []
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to update like status.'
        ], $response['status'] ?? 400);
    }

    public function comment(Request $request, $id)
    {
        if (!$this->authService->isAuthenticated()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to comment.'
            ], 401);
        }

        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $response = $this->communityService->commentOnPost($id, $request->comment);

        if ($response['success']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $response['message'] ?? 'Comment added successfully!',
                    'data' => $response['data'] ?? []
                ]);
            }
            
            return redirect()->back()
                ->with('success', $response['message'] ?? 'Comment added successfully!');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Failed to add comment.'
            ], $response['status'] ?? 400);
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to add comment.')
            ->withInput();
    }
}
