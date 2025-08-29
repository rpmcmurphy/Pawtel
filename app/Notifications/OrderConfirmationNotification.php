<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification implements ShouldQueue
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
        $message = (new MailMessage)
            ->subject('Order Confirmation - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your order has been placed successfully.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total Items: ' . $this->order->orderItems->sum('quantity'))
            ->line('Subtotal: $' . number_format($this->order->subtotal, 2))
            ->line('Delivery Charge: $' . number_format($this->order->delivery_charge, 2))
            ->line('Total Amount: $' . number_format($this->order->total_amount, 2))
            ->line('Delivery Address: ' . $this->order->delivery_address)
            ->line('Payment Method: Cash on Delivery (COD)')
            ->line('We will contact you shortly to confirm delivery details.')
            ->action('View Order Details', url('/orders/' . $this->order->order_number))
            ->line('Thank you for shopping with Pawtel!');

        return $message;
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'message' => 'Your order has been placed successfully',
            'total_amount' => $this->order->total_amount,
            'items_count' => $this->order->orderItems->sum('quantity'),
        ];
    }
}
