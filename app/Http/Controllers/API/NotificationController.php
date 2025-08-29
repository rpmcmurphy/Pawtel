<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = $user->notifications();

            // Filter by read status
            if ($request->has('unread_only') && $request->boolean('unread_only')) {
                $query->whereNull('read_at');
            }

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', 'like', '%' . $request->type . '%');
            }

            $notifications = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            $formattedNotifications = $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $this->getNotificationType($notification->type),
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'data' => $notification->data,
                    'read_at' => $notification->read_at?->format('Y-m-d H:i:s'),
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'is_read' => $notification->read_at !== null,
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedNotifications,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                ],
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead(string $notificationId): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($notificationId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => [
                    'unread_count' => $user->unreadNotifications()->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'data' => [
                    'unread_count' => 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $notificationId): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($notificationId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'data' => [
                    'unread_count' => $user->unreadNotifications()->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getNotificationType(string $type): string
    {
        $typeMap = [
            'App\\Notifications\\BookingConfirmationNotification' => 'booking_confirmation',
            'App\\Notifications\\BookingCancellationNotification' => 'booking_cancellation',
            'App\\Notifications\\BookingReminderNotification' => 'booking_reminder',
            'App\\Notifications\\OrderConfirmationNotification' => 'order_confirmation',
            'App\\Notifications\\OrderStatusNotification' => 'order_status',
        ];

        return $typeMap[$type] ?? 'general';
    }

    private function getNotificationTitle($notification): string
    {
        $data = $notification->data;
        $type = $this->getNotificationType($notification->type);

        switch ($type) {
            case 'booking_confirmation':
                return 'Booking Confirmed';
            case 'booking_cancellation':
                return 'Booking Cancelled';
            case 'booking_reminder':
                return 'Booking Reminder';
            case 'order_confirmation':
                return 'Order Placed';
            case 'order_status':
                return 'Order Update';
            default:
                return 'Notification';
        }
    }

    private function getNotificationMessage($notification): string
    {
        $data = $notification->data;
        $type = $this->getNotificationType($notification->type);

        switch ($type) {
            case 'booking_confirmation':
                return "Your {$data['type']} booking ({$data['booking_number']}) has been confirmed.";
            case 'booking_cancellation':
                return "Your {$data['type']} booking ({$data['booking_number']}) has been cancelled.";
            case 'booking_reminder':
                return "Reminder: Your {$data['type']} booking is tomorrow.";
            case 'order_confirmation':
                return "Your order ({$data['order_number']}) has been placed successfully.";
            case 'order_status':
                return "Your order ({$data['order_number']}) status has been updated to {$data['status']}.";
            default:
                return $data['message'] ?? 'You have a new notification.';
        }
    }
}
