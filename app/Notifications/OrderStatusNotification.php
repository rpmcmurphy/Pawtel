<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusMessages = [
            'processing' => 'Your order is being processed',
            'shipped' => 'Your order has been shipped',
            'delivered' => 'Your order has been delivered',
            'cancelled' => 'Your order has been cancelled',
        ];

        $message = (new MailMessage)
            ->subject('Order Update - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($statusMessages[$this->order->status] ?? 'Your order status has been updated')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Status: ' . ucfirst($this->order->status))
            ->line('Total Amount: $' . number_format($this->order->total_amount, 2));

        if ($this->order->status === 'delivered') {
            $message->line('Delivered At: ' . $this->order->delivered_at->format('M d, Y h:i A'))
                ->line('Thank you for your purchase!');
        } elseif ($this->order->status === 'cancelled') {
            $message->line('Cancelled At: ' . $this->order->cancelled_at->format('M d, Y h:i A'))
                ->line('If you have any questions, please contact our support team.');
        }

        return $message->action('View Order Details', url('/orders/' . $this->order->order_number))
            ->line('Thank you for choosing Pawtel!');
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'message' => 'Your order status has been updated to ' . $this->order->status,
        ];
    }
}
