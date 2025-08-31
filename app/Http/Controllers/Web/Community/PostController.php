<?php

namespace App\Http\Controllers\Web\Community;

use App\Http\Controllers\Controller;
use App\Services\Web\CommunityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $communityService;

    public function __construct(CommunityService $communityService)
    {
        $this->communityService = $communityService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['category', 'search', 'sort', 'page']);
        $posts = $this->communityService->getPosts($params);

        return view('community.posts', [
            'posts' => $posts['success'] ? $posts['data'] : [],
            'filters' => $params
        ]);
    }

    public function show($slug)
    {
        $post = $this->communityService->getPost($slug);

        if (!$post['success']) {
            return redirect()->route('community.posts')
                ->with('error', 'Post not found.');
        }

        // Get comments
        $comments = $this->communityService->getPostComments($post['data']['id']);

        return view('community.post', [
            'post' => $post['data'],
            'comments' => $comments['success'] ? $comments['data'] : []
        ]);
    }

    public function like(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to like posts.'
            ], 401);
        }

        $response = $this->communityService->likePost($id);

        return response()->json($response);
    }

    public function comment(Request $request, $id)
    {
        if (!Auth::check()) {
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
            return redirect()->back()
                ->with('success', 'Comment added successfully!');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to add comment.')
            ->withInput();
    }
}
