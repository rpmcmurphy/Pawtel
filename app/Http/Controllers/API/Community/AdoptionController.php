<?php

namespace App\Http\Controllers\API\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdoptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Post::with(['user', 'adoptionDetail'])
                ->ofType('adoption')
                ->published()
                ->whereHas('adoptionDetail', function ($q) use ($request) {
                    if ($request->has('status')) {
                        $q->where('status', $request->status);
                    } else {
                        $q->available(); // Default to available cats
                    }

                    if ($request->has('gender')) {
                        $q->where('gender', $request->gender);
                    }

                    if ($request->has('breed')) {
                        $q->where('breed', 'like', "%{$request->breed}%");
                    }
                });

            // Search functionality
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('content', 'like', "%{$searchTerm}%")
                        ->orWhereHas('adoptionDetail', function ($subQ) use ($searchTerm) {
                            $subQ->where('cat_name', 'like', "%{$searchTerm}%")
                                ->orWhere('breed', 'like', "%{$searchTerm}%");
                        });
                });
            }

            $posts = $query->orderBy('published_at', 'desc')
                ->paginate($request->get('per_page', 12));

            return response()->json([
                'success' => true,
                'data' => PostResource::collection($posts->items()),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'last_page' => $posts->lastPage(),
                ],
                'filters' => [
                    'available_genders' => ['male', 'female', 'unknown'],
                    'common_breeds' => $this->getCommonBreeds(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch adoption listings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $post = Post::with(['user', 'adoptionDetail', 'comments.user'])
                ->ofType('adoption')
                ->published()
                ->find($id);

            if (!$post || !$post->adoptionDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adoption listing not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch adoption listing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getCommonBreeds(): array
    {
        return [
            'Persian',
            'British Shorthair',
            'Maine Coon',
            'Ragdoll',
            'Bengal',
            'Siamese',
            'Russian Blue',
            'Abyssinian',
            'Scottish Fold',
            'Mixed Breed',
            'Unknown'
        ];
    }
}
