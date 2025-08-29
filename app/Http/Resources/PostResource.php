<?php
// app/Http/Resources/PostResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'featured_image' => $this->featured_image,
            'images' => $this->images,
            'status' => $this->status,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Engagement metrics
            'likes_count' => $this->getLikesCount(),
            'comments_count' => $this->getCommentsCount(),
            'is_liked' => $this->when(
                auth()->check(),
                $this->isLikedByUser(auth()->id())
            ),

            // Author information
            'author' => $this->when(
                $this->relationLoaded('user'),
                [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ]
            ),

            // Adoption details
            'adoption_details' => $this->when(
                $this->relationLoaded('adoptionDetail') && $this->adoptionDetail,
                [
                    'cat_name' => $this->adoptionDetail->cat_name,
                    'age' => $this->adoptionDetail->age,
                    'gender' => $this->adoptionDetail->gender,
                    'breed' => $this->adoptionDetail->breed,
                    'health_status' => $this->adoptionDetail->health_status,
                    'adoption_fee' => $this->adoptionDetail->adoption_fee,
                    'contact_info' => $this->adoptionDetail->contact_info,
                    'status' => $this->adoptionDetail->status,
                ]
            ),

            // Comments (limited)
            'recent_comments' => $this->when(
                $this->relationLoaded('comments'),
                function () {
                    return $this->comments()
                        ->approved()
                        ->with('user')
                        ->latest()
                        ->take(3)
                        ->get()
                        ->map(function ($comment) {
                            return [
                                'id' => $comment->id,
                                'comment' => $comment->comment,
                                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                                'user' => [
                                    'id' => $comment->user->id,
                                    'name' => $comment->user->name,
                                ],
                            ];
                        });
                }
            ),
        ];
    }
}
