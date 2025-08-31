<?php

namespace App\Services\Web;

class AdminService extends ApiService
{
    // Dashboard Methods
    public function getDashboardStats()
    {
        return $this->get('admin/dashboard');
    }

    public function getRecentActivity()
    {
        return $this->get('admin/dashboard/recent-activity');
    }

    // Booking Management
    public function getAdminBookings($params = [])
    {
        return $this->get('admin/bookings', $params);
    }

    public function getAdminBooking($id)
    {
        return $this->get("admin/bookings/{$id}");
    }

    public function updateBookingStatus($id, $status, $notes = null)
    {
        return $this->put("admin/bookings/{$id}/status", [
            'status' => $status,
            'notes' => $notes
        ]);
    }

    public function confirmBooking($id)
    {
        return $this->post("admin/bookings/{$id}/confirm");
    }

    public function cancelBooking($id, $reason)
    {
        return $this->post("admin/bookings/{$id}/cancel", ['reason' => $reason]);
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

    public function getUserBookings($id, $params = [])
    {
        return $this->get("admin/users/{$id}/bookings", $params);
    }

    public function getUserOrders($id, $params = [])
    {
        return $this->get("admin/users/{$id}/orders", $params);
    }

    // Reports
    public function getBookingReports($params = [])
    {
        return $this->get('admin/reports/bookings', $params);
    }

    public function getSalesReports($params = [])
    {
        return $this->get('admin/reports/sales', $params);
    }

    public function getFinancialReports($params = [])
    {
        return $this->get('admin/reports/financial', $params);
    }

    public function exportReport($type, $params = [])
    {
        return $this->post("admin/reports/{$type}/export", $params);
    }

    // Room Management
    public function getAdminRooms($params = [])
    {
        return $this->get('admin/rooms', $params);
    }

    public function blockRoom($roomId, $dates, $reason)
    {
        return $this->post("admin/rooms/{$roomId}/block", [
            'dates' => $dates,
            'reason' => $reason
        ]);
    }

    public function unblockRoom($roomId, $dates)
    {
        return $this->post("admin/rooms/{$roomId}/unblock", ['dates' => $dates]);
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

    // Community Management
    public function getAdminPosts($params = [])
    {
        return $this->get('admin/posts', $params);
    }

    public function getAdminPost($id)
    {
        return $this->get("admin/posts/{$id}");
    }

    public function updatePostStatus($id, $status)
    {
        return $this->put("admin/posts/{$id}/status", ['status' => $status]);
    }

    public function deletePost($id)
    {
        return $this->delete("admin/posts/{$id}");
    }

    public function approveComment($commentId)
    {
        return $this->post("admin/comments/{$commentId}/approve");
    }

    public function rejectComment($commentId)
    {
        return $this->post("admin/comments/{$commentId}/reject");
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
