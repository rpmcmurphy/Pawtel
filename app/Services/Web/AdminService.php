<?php

namespace App\Services\Web;

class AdminService extends ApiService
{
    // Dashboard Methods
    public function getDashboardStats()
    {
        return $this->get('admin/dashboard/stats');
    }

    public function getRecentActivities()
    {
        return $this->get('admin/dashboard/recent-activities');
    }

    public function getBookingStats()
    {
        return $this->get('admin/dashboard/booking-stats');
    }

    // Booking Management Methods
    public function getAdminBookings($params = [])
    {
        return $this->get('admin/bookings', $params);
    }

    public function getAdminBooking($id)
    {
        return $this->get("admin/bookings/{$id}");
    }

    public function updateBookingStatus($id, $status)
    {
        return $this->put("admin/bookings/{$id}/status", ['status' => $status]);
    }

    public function confirmBooking($id, $data = [])
    {
        return $this->put("admin/bookings/{$id}/confirm", $data);
    }

    public function cancelBooking($id, $reason)
    {
        return $this->put("admin/bookings/{$id}/cancel", ['reason' => $reason]);
    }

    public function assignRoom($bookingId, $roomId)
    {
        return $this->post("admin/bookings/{$bookingId}/assign-room", ['room_id' => $roomId]);
    }

    // Product Management
    public function getAdminProducts($params = [])
    {
        return $this->get('admin/products', $params);
    }

    public function getAdminProduct($id)
    {
        return $this->get("admin/products/{$id}");
    }

    public function createProduct($productData)
    {
        return $this->post('admin/products', $productData);
    }

    public function updateProduct($id, $productData)
    {
        return $this->put("admin/products/{$id}", $productData);
    }

    public function deleteProduct($id)
    {
        return $this->delete("admin/products/{$id}");
    }

    public function updateProductStatus($id, $status)
    {
        return $this->put("admin/products/{$id}/status", ['status' => $status]);
    }


    public function uploadProductImage($file)
    {
        return $this->upload('admin/products/upload-image', ['image' => $file]);
    }

    // User Management
    public function getAdminUsers($params = [])
    {
        return $this->get('admin/users', $params);
    }

    public function getAdminUser($id)
    {
        return $this->get("admin/users/{$id}");
    }

    public function updateUserStatus($id, $status)
    {
        return $this->put("admin/users/{$id}/status", ['status' => $status]);
    }

    public function getUserBookings($userId, $params = [])
    {
        return $this->get("admin/users/{$userId}/bookings", $params);
    }

    public function getUserOrders($userId, $params = [])
    {
        return $this->get("admin/users/{$userId}/orders", $params);
    }

    // Order Management
    public function getAdminOrders($params = [])
    {
        return $this->get('admin/orders', $params);
    }

    public function getAdminOrder($id)
    {
        return $this->get("admin/orders/{$id}");
    }

    public function updateOrderStatus($id, $status)
    {
        return $this->put("admin/orders/{$id}/status", ['status' => $status]);
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

    /**
     * Create a manual booking through admin interface
     */
    public function createManualBooking($bookingData)
    {
        return $this->post('admin/bookings/manual', $bookingData);
    }

    /**
     * Update a booking
     */
    public function updateBooking($id, $bookingData)
    {
        return $this->put("admin/bookings/{$id}", $bookingData);
    }

    /**
     * Search customers for admin booking creation
     */
    public function searchCustomers($searchTerm)
    {
        return $this->get('admin/users/customers/search', ['search' => $searchTerm]);
    }

    /**
     * Get room types for booking forms
     */
    public function getRoomTypes()
    {
        return $this->get('admin/rooms/types/list');
    }

    /**
     * Get spa packages
     */
    public function getSpaPackages()
    {
        return $this->get('spa/packages');
    }

    /**
     * Get spay packages
     */
    public function getSpayPackages()
    {
        return $this->get('spay/packages');
    }

    /**
     * Get addon services
     */
    public function getAddonServices($category = null)
    {
        $params = $category ? ['category' => $category] : [];
        return $this->get('addon-services', $params);
    }

    // Post Management Methods
    public function getAdminPosts($params = [])
    {
        return $this->get('admin/posts', $params);
    }

    public function getAdminPost($id)
    {
        return $this->get("admin/posts/{$id}");
    }

    public function createPost($postData)
    {
        return $this->post('admin/posts', $postData);
    }

    public function updatePost($id, $postData)
    {
        return $this->put("admin/posts/{$id}", $postData);
    }

    public function deletePost($id)
    {
        return $this->delete("admin/posts/{$id}");
    }

    public function publishPost($id)
    {
        return $this->post("admin/posts/{$id}/publish");
    }

    public function archivePost($id)
    {
        return $this->post("admin/posts/{$id}/archive");
    }

    public function getPendingComments()
    {
        return $this->get('admin/posts/comments/pending');
    }

    public function approveComment($commentId)
    {
        return $this->put("admin/posts/comments/{$commentId}/approve");
    }

    public function rejectComment($commentId)
    {
        return $this->put("admin/posts/comments/{$commentId}/reject");
    }
}