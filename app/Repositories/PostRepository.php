<?php
// app/Repositories/PostRepository.php
namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public function findOrFail(int $id): Post
    {
        return Post::findOrFail($id);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(int $id, array $data): Post
    {
        $post = $this->findOrFail($id);
        $post->update($data);
        return $post->fresh();
    }

    public function delete(int $id): bool
    {
        return Post::destroy($id) > 0;
    }

    public function getWithFilters(array $filters, int $perPage = 15)
    {
        $query = Post::with(['user', 'adoptionDetail']);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
