<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;
use App\Models\User;
use App\Notifications\BookingConfirmationNotification;
use App\Notifications\BookingCancellationNotification;
use App\Notifications\BookingReminderNotification;
use App\Notifications\OrderConfirmationNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendBookingConfirmation(Booking $booking): void
    {
        try {
            $booking->user->notify(new BookingConfirmationNotification($booking));

            // Update confirmation sent timestamp
            $booking->update(['confirmation_sent_at' => now()]);

            Log::info('Booking confirmation sent', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'user_email' => $booking->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function sendBookingCancellation(Booking $booking): void
    {
        try {
            $booking->user->notify(new BookingCancellationNotification($booking));

            Log::info('Booking cancellation sent', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'user_email' => $booking->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function sendBookingReminder(Booking $booking): void
    {
        try {
            $booking->user->notify(new BookingReminderNotification($booking));

            Log::info('Booking reminder sent', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'user_email' => $booking->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking reminder', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendOrderConfirmation(Order $order): void
    {
        try {
            $order->user->notify(new OrderConfirmationNotification($order));

            Log::info('Order confirmation sent', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_email' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function sendOrderStatusUpdate(Order $order): void
    {
        try {
            $order->user->notify(new OrderStatusNotification($order));

            Log::info('Order status update sent', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'user_email' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order status update', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendWelcomeEmail(User $user): void
    {
        try {
            // Send welcome email with basic information
            Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Welcome to Pawtel - Your Pet Care Partner');
            });

            Log::info('Welcome email sent', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendAdminNotification(string $type, array $data): void
    {
        try {
            $adminEmails = User::role('admin')->pluck('email');

            foreach ($adminEmails as $email) {
                Mail::send("emails.admin.{$type}", $data, function ($message) use ($email, $type) {
                    $message->to($email)
                        ->subject("Pawtel Admin: " . ucwords(str_replace('_', ' ', $type)));
                });
            }

            Log::info('Admin notification sent', [
                'type' => $type,
                'recipients' => $adminEmails->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
