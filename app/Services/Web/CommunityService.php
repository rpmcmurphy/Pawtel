<?php

namespace App\Services\Web;

class CommunityService extends ApiService
{
    // Post Methods
    public function getPosts($params = [])
    {
        // Map web params to API params
        $apiParams = [];
        if (isset($params['category'])) {
            $apiParams['type'] = $params['category'];
        }
        if (isset($params['search'])) {
            $apiParams['search'] = $params['search'];
        }
        if (isset($params['sort'])) {
            // Map sort options
            switch ($params['sort']) {
                case 'latest':
                    $apiParams['sort_by'] = 'published_at';
                    $apiParams['sort_order'] = 'desc';
                    break;
                case 'popular':
                    // API doesn't have likes sort, use published_at desc as fallback
                    $apiParams['sort_by'] = 'published_at';
                    $apiParams['sort_order'] = 'desc';
                    break;
                case 'commented':
                    // API doesn't have comments sort, use published_at desc as fallback
                    $apiParams['sort_by'] = 'published_at';
                    $apiParams['sort_order'] = 'desc';
                    break;
            }
        }
        if (isset($params['page'])) {
            $apiParams['page'] = $params['page'];
        }
        $apiParams['per_page'] = $params['per_page'] ?? 10;

        $response = $this->get('community/posts', $apiParams);
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data'],
                'pagination' => $response['data']['pagination'] ?? null
            ];
        }
        
        return $response;
    }

    public function getPost($slug)
    {
        $response = $this->get("community/posts/{$slug}");
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data']
            ];
        }
        
        return $response;
    }

    public function likePost($postId)
    {
        $response = $this->post("posts/{$postId}/like");
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data'],
                'message' => $response['data']['message'] ?? 'Post liked successfully'
            ];
        }
        
        return $response;
    }

    public function unlikePost($postId)
    {
        $response = $this->delete("posts/{$postId}/unlike");
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data'],
                'message' => $response['data']['message'] ?? 'Post unliked successfully'
            ];
        }
        
        return $response;
    }

    public function commentOnPost($postId, $comment)
    {
        $response = $this->post("posts/{$postId}/comment", ['comment' => $comment]);
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data'],
                'message' => $response['data']['message'] ?? 'Comment added successfully'
            ];
        }
        
        return $response;
    }

    // Adoption Methods
    public function getAdoptions($params = [])
    {
        // Map web params to API params
        $apiParams = [];
        if (isset($params['age'])) {
            // API doesn't have age filter directly, might need to search
        }
        if (isset($params['gender'])) {
            $apiParams['gender'] = $params['gender'];
        }
        if (isset($params['breed'])) {
            $apiParams['breed'] = $params['breed'];
        }
        if (isset($params['location'])) {
            // API doesn't have location filter, might need to search
        }
        if (isset($params['search'])) {
            $apiParams['search'] = $params['search'];
        }
        if (isset($params['page'])) {
            $apiParams['page'] = $params['page'];
        }
        $apiParams['per_page'] = $params['per_page'] ?? 12;

        $response = $this->get('community/adoptions', $apiParams);
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data'],
                'pagination' => $response['data']['pagination'] ?? null
            ];
        }
        
        return $response;
    }

    public function getAdoption($id)
    {
        $response = $this->get("community/adoptions/{$id}");
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data']
            ];
        }
        
        return $response;
    }

    public function expressInterest($adoptionId, $message = null)
    {
        // Note: This endpoint might not exist in API yet, keeping for future
        $response = $this->post("adoptions/{$adoptionId}/interest", ['message' => $message]);
        
        // Handle API response structure
        if ($response['success'] && isset($response['data']['data'])) {
            return [
                'success' => true,
                'data' => $response['data']['data'],
                'message' => $response['data']['message'] ?? 'Interest expressed successfully'
            ];
        }
        
        return $response;
    }
}
