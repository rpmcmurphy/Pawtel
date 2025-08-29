<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancellationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Cancelled - ' . $this->booking->booking_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been cancelled.')
            ->line('Booking Number: ' . $this->booking->booking_number)
            ->line('Service Type: ' . ucfirst($this->booking->type))
            ->line('Cancellation Reason: ' . $this->booking->cancellation_reason)
            ->line('Cancelled At: ' . $this->booking->cancelled_at->format('M d, Y h:i A'))
            ->line('If you have any questions, please contact our support team.')
            ->action('Contact Support', url('/contact'))
            ->line('We hope to serve you again in the future!');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'type' => $this->booking->type,
            'message' => 'Your booking has been cancelled',
            'reason' => $this->booking->cancellation_reason,
        ];
    }
}
