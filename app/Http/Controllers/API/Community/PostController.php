<?php

namespace App\Http\Controllers\API\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Post::with(['user', 'adoptionDetail'])
                ->published();

            // Filter by post type
            if ($request->has('type')) {
                $query->ofType($request->type);
            }

            // Search functionality
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('content', 'like', "%{$searchTerm}%");
                });
            }

            // Order by
            $sortBy = $request->get('sort_by', 'published_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'title':
                    $query->orderBy('title', $sortOrder);
                    break;
                case 'created_at':
                    $query->orderBy('created_at', $sortOrder);
                    break;
                default:
                    $query->orderBy('published_at', $sortOrder);
            }

            $posts = $query->paginate($request->get('per_page', 10));

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

    public function show(string $slug): JsonResponse
    {
        try {
            $post = Post::with(['user', 'adoptionDetail', 'comments.user'])
                ->where('slug', $slug)
                ->published()
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function like(int $id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $post = Post::published()->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            $user = Auth::user();

            // Check if already liked
            $existingLike = PostLike::where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingLike) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post already liked'
                ], 400);
            }

            // Create like
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post liked successfully',
                'data' => [
                    'likes_count' => $post->getLikesCount() + 1,
                    'is_liked' => true,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unlike(int $id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $post = Post::published()->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            $user = Auth::user();

            $like = PostLike::where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$like) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not liked yet'
                ], 400);
            }

            $like->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post unliked successfully',
                'data' => [
                    'likes_count' => max(0, $post->getLikesCount() - 1),
                    'is_liked' => false,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlike post',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
