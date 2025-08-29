<?php

namespace App\Http\Controllers\API\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\CreateCommentRequest;
use App\Http\Requests\Community\UpdateCommentRequest;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(int $postId, CreateCommentRequest $request): JsonResponse
    {
        try {
            $post = Post::published()->find($postId);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            $comment = PostComment::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'comment' => $request->comment,
                'status' => 'approved', // Auto-approve for now
            ]);

            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'comment' => [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'status' => $comment->status,
                        'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                        ],
                    ],
                    'comments_count' => $post->getCommentsCount() + 1,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(int $commentId, UpdateCommentRequest $request): JsonResponse
    {
        try {
            $comment = PostComment::with('user')
                ->where('id', $commentId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found or unauthorized'
                ], 404);
            }

            // Only allow editing within 24 hours
            if ($comment->created_at->diffInHours(now()) > 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comments can only be edited within 24 hours'
                ], 400);
            }

            $comment->update([
                'comment' => $request->comment,
                'status' => 'pending', // Set to pending for re-moderation
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'data' => [
                    'comment' => [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'status' => $comment->status,
                        'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                        ],
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $commentId): JsonResponse
    {
        try {
            $comment = PostComment::where('id', $commentId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found or unauthorized'
                ], 404);
            }

            // Only allow deletion within 24 hours
            if ($comment->created_at->diffInHours(now()) > 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comments can only be deleted within 24 hours'
                ], 400);
            }

            $postId = $comment->post_id;
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully',
                'data' => [
                    'post_id' => $postId,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
