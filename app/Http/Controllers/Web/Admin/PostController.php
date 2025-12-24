<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Web\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['type', 'status', 'search', 'page']);
        $posts = $this->adminService->getAdminPosts($params);

        return view('admin.posts.index', [
            'posts' => $posts['success'] ? $posts['data'] : [],
            'filters' => $params
        ]);
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:adoption,story,news,job',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'images' => 'nullable',
            'status' => 'required|in:draft,published,archived',

            // Adoption-specific fields - only validate if type is adoption
            'adoption.cat_name' => 'required_if:type,adoption|nullable|string|max:255',
            'adoption.age' => 'nullable|string|max:50',
            'adoption.gender' => 'nullable|in:male,female,unknown',
            'adoption.breed' => 'nullable|string|max:255',
            'adoption.health_status' => 'nullable|string',
            'adoption.adoption_fee' => 'nullable|numeric|min:0',
            'adoption.contact_info' => 'nullable',
            'adoption.status' => 'nullable|in:available,pending,adopted',
        ]);

        // Remove adoption fields if type is not adoption
        if ($validated['type'] !== 'adoption') {
            unset($validated['adoption']);
        }

        // Parse JSON fields if they are strings
        if (isset($validated['images']) && is_string($validated['images'])) {
            $validated['images'] = json_decode($validated['images'], true) ?? [];
        }
        if (!isset($validated['images']) || !is_array($validated['images'])) {
            $validated['images'] = [];
        }

        // Only process adoption fields if type is adoption
        if ($validated['type'] === 'adoption' && isset($validated['adoption'])) {
            if (isset($validated['adoption']['contact_info']) && is_string($validated['adoption']['contact_info'])) {
                $validated['adoption']['contact_info'] = json_decode($validated['adoption']['contact_info'], true) ?? [];
            }
            if (!isset($validated['adoption']['contact_info']) || !is_array($validated['adoption']['contact_info'])) {
                $validated['adoption']['contact_info'] = [];
            }
        }

        $response = $this->adminService->createPost($validated);

        if ($response['success']) {
            // Handle nested API response structure
            $postData = $response['data']['data'] ?? $response['data'] ?? null;
            $postId = $postData['id'] ?? null;
            
            if ($postId) {
                return redirect()->route('admin.posts.show', $postId)
                    ->with('success', 'Post created successfully.');
            }
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $response['data']['message'] ?? $response['message'] ?? 'Failed to create post.');
    }

    public function show($id)
    {
        $post = $this->adminService->getAdminPost($id);

        if (!$post['success']) {
            return redirect()->route('admin.posts.index')
                ->with('error', 'Post not found.');
        }

        // Handle nested API response structure
        $postData = $post['data']['data'] ?? $post['data'] ?? null;
        
        if (!$postData) {
            return redirect()->route('admin.posts.index')
                ->with('error', 'Post data not found.');
        }

        return view('admin.posts.show', [
            'post' => $postData
        ]);
    }

    public function edit($id)
    {
        $post = $this->adminService->getAdminPost($id);

        if (!$post['success']) {
            return redirect()->route('admin.posts.index')
                ->with('error', 'Post not found.');
        }

        // Handle nested API response structure
        $postData = $post['data']['data'] ?? $post['data'] ?? null;
        
        if (!$postData) {
            return redirect()->route('admin.posts.index')
                ->with('error', 'Post data not found.');
        }

        return view('admin.posts.edit', [
            'post' => $postData
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:adoption,story,news,job',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'images' => 'nullable',
            'status' => 'required|in:draft,published,archived',

            // Adoption-specific fields - only validate if type is adoption
            'adoption.cat_name' => 'required_if:type,adoption|nullable|string|max:255',
            'adoption.age' => 'nullable|string|max:50',
            'adoption.gender' => 'nullable|in:male,female,unknown',
            'adoption.breed' => 'nullable|string|max:255',
            'adoption.health_status' => 'nullable|string',
            'adoption.adoption_fee' => 'nullable|numeric|min:0',
            'adoption.contact_info' => 'nullable',
            'adoption.status' => 'nullable|in:available,pending,adopted',
        ]);

        // Remove adoption fields if type is not adoption
        if ($validated['type'] !== 'adoption') {
            unset($validated['adoption']);
        }

        // Parse JSON fields if they are strings
        if (isset($validated['images']) && is_string($validated['images'])) {
            $validated['images'] = json_decode($validated['images'], true) ?? [];
        }
        if (!isset($validated['images']) || !is_array($validated['images'])) {
            $validated['images'] = [];
        }

        // Only process adoption fields if type is adoption
        if ($validated['type'] === 'adoption' && isset($validated['adoption'])) {
            if (isset($validated['adoption']['contact_info']) && is_string($validated['adoption']['contact_info'])) {
                $validated['adoption']['contact_info'] = json_decode($validated['adoption']['contact_info'], true) ?? [];
            }
            if (!isset($validated['adoption']['contact_info']) || !is_array($validated['adoption']['contact_info'])) {
                $validated['adoption']['contact_info'] = [];
            }
        }

        $response = $this->adminService->updatePost($id, $validated);

        if ($response['success']) {
            return redirect()->route('admin.posts.show', $id)
                ->with('success', 'Post updated successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $response['data']['message'] ?? $response['message'] ?? 'Failed to update post.');
    }

    public function destroy($id)
    {
        $response = $this->adminService->deletePost($id);

        if ($response['success']) {
            return redirect()->route('admin.posts.index')
                ->with('success', 'Post deleted successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to delete post.');
    }

    public function publish($id)
    {
        $response = $this->adminService->publishPost($id);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Post published successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to publish post.');
    }

    public function archive($id)
    {
        $response = $this->adminService->archivePost($id);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Post archived successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to archive post.');
    }

    public function pendingComments()
    {
        $comments = $this->adminService->getPendingComments();

        return view('admin.posts.comments', [
            'comments' => $comments['success'] ? $comments['data'] : []
        ]);
    }

    public function approveComment($commentId)
    {
        $response = $this->adminService->approveComment($commentId);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Comment approved successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to approve comment.');
    }

    public function rejectComment($commentId)
    {
        $response = $this->adminService->rejectComment($commentId);

        if ($response['success']) {
            return redirect()->back()
                ->with('success', 'Comment rejected successfully.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Failed to reject comment.');
    }
}