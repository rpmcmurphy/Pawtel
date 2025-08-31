<?php

namespace App\Services\Web;

class CommunityService extends ApiService
{
    // Post Methods
    public function getPosts($params = [])
    {
        return $this->get('posts', $params);
    }

    public function getPost($slug)
    {
        return $this->get("posts/{$slug}");
    }

    public function likePost($postId)
    {
        return $this->post("posts/{$postId}/like");
    }

    public function unlikePost($postId)
    {
        return $this->delete("posts/{$postId}/unlike");
    }

    public function commentOnPost($postId, $comment)
    {
        return $this->post("posts/{$postId}/comment", ['content' => $comment]);
    }

    public function getPostComments($postId, $params = [])
    {
        return $this->get("posts/{$postId}/comments", $params);
    }

    // Adoption Methods
    public function getAdoptions($params = [])
    {
        return $this->get('adoptions', $params);
    }

    public function getAdoption($slug)
    {
        return $this->get("adoptions/{$slug}");
    }

    public function expressInterest($adoptionId, $message = null)
    {
        return $this->post("adoptions/{$adoptionId}/interest", ['message' => $message]);
    }
}
