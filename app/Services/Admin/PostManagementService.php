<?php
// app/Services/Admin/PostManagementService.php
namespace App\Services\Admin;

use App\Models\{Post, AdoptionDetail};
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostManagementService
{
    public function __construct(
        private PostRepository $postRepo
    ) {}

    public function createPost(array $data): Post
    {
        DB::beginTransaction();
        try {
            $postData = [
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'content' => $data['content'],
                'featured_image' => $data['featured_image'] ?? null,
                'images' => $data['images'] ?? [],
                'status' => $data['status'],
                'published_at' => $data['status'] === 'published' ? now() : null,
            ];

            $post = $this->postRepo->create($postData);

            if ($data['type'] === 'adoption' && isset($data['adoption'])) {
                $this->createAdoptionDetails($post->id, $data['adoption']);
            }

            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updatePost(int $id, array $data): Post
    {
        DB::beginTransaction();
        try {
            $updateData = [
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'content' => $data['content'],
                'featured_image' => $data['featured_image'] ?? null,
                'images' => $data['images'] ?? [],
                'status' => $data['status'],
            ];

            if ($data['status'] === 'published') {
                $post = $this->postRepo->findOrFail($id);
                if (!$post->published_at) {
                    $updateData['published_at'] = now();
                }
            }

            $post = $this->postRepo->update($id, $updateData);

            if ($post->type === 'adoption' && isset($data['adoption'])) {
                $this->updateAdoptionDetails($post, $data['adoption']);
            }

            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createAdoptionDetails(int $postId, array $adoptionData): void
    {
        AdoptionDetail::create([
            'post_id' => $postId,
            'cat_name' => $adoptionData['cat_name'],
            'age' => $adoptionData['age'] ?? null,
            'gender' => $adoptionData['gender'] ?? null,
            'breed' => $adoptionData['breed'] ?? null,
            'health_status' => $adoptionData['health_status'] ?? null,
            'adoption_fee' => $adoptionData['adoption_fee'] ?? null,
            'contact_info' => $adoptionData['contact_info'] ?? [],
            'status' => $adoptionData['status'] ?? 'available',
        ]);
    }

    private function updateAdoptionDetails(Post $post, array $adoptionData): void
    {
        if ($post->adoptionDetail) {
            $post->adoptionDetail->update([
                'cat_name' => $adoptionData['cat_name'],
                'age' => $adoptionData['age'] ?? null,
                'gender' => $adoptionData['gender'] ?? null,
                'breed' => $adoptionData['breed'] ?? null,
                'health_status' => $adoptionData['health_status'] ?? null,
                'adoption_fee' => $adoptionData['adoption_fee'] ?? null,
                'contact_info' => $adoptionData['contact_info'] ?? [],
                'status' => $adoptionData['status'] ?? 'available',
            ]);
        }
    }
}
