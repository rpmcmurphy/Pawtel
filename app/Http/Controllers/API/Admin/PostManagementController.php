<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Requests\Admin\{CreatePostRequest, UpdatePostRequest};
use App\Services\Admin\PostManagementService;
use App\Repositories\PostRepository;
use App\Models\{PostComment, SpaPackage, SpayPackage, AddonService};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Str;

class PostManagementController extends Controller
{
    public function __construct(
        private PostManagementService $postService,
        private PostRepository $postRepo
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $posts = $this->postRepo->getWithFilters(
                $request->only(['type', 'status', 'search']),
                $request->get('per_page', 15)
            );

            return response()->json([
                'success' => true,
                'data' => PostResource::collection($posts->items()),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'last_page' => $posts->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreatePostRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->createPost($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => new PostResource($post->load(['adoptionDetail']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $post = $this->postRepo->findOrFail($id);
            $post->load(['user', 'adoptionDetail', 'comments.user']);

            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }
    }

    public function update(int $id, UpdatePostRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->updatePost($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => new PostResource($post->load(['adoptionDetail']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->postRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }
    }

    public function publish(int $id): JsonResponse
    {
        try {
            $post = $this->postRepo->update($id, [
                'status' => 'published',
                'published_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post published successfully',
                'data' => new PostResource($post)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }
    }

    public function approveComment(int $commentId): JsonResponse
    {
        try {
            PostComment::findOrFail($commentId)->update(['status' => 'approved']);

            return response()->json([
                'success' => true,
                'message' => 'Comment approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found',
            ], 404);
        }
    }

    public function rejectComment(int $commentId): JsonResponse
    {
        try {
            PostComment::findOrFail($commentId)->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => 'Comment rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found',
            ], 404);
        }
    }

    // Service Package Management
    public function spaPackages(): JsonResponse
    {
        try {
            $packages = SpaPackage::orderBy('sort_order')->get();
            return response()->json(['success' => true, 'data' => $packages]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch spa packages'], 500);
        }
    }

    public function storeSpaPackage(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'max_daily_bookings' => 'required|integer|min:1',
        ]);

        try {
            $package = SpaPackage::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'price' => $request->price,
                'max_daily_bookings' => $request->max_daily_bookings,
                'status' => 'active',
                'sort_order' => SpaPackage::max('sort_order') + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Spa package created successfully',
                'data' => $package
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create spa package'], 500);
        }
    }

    public function addonServices(): JsonResponse
    {
        try {
            $services = AddonService::orderBy('category')->orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $services]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch addon services'], 500);
        }
    }
}
