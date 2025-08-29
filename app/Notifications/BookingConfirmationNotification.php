<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmationNotification extends Notification implements ShouldQueue
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
        $message = (new MailMessage)
            ->subject('Booking Confirmation - ' . $this->booking->booking_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been confirmed successfully.')
            ->line('Booking Number: ' . $this->booking->booking_number)
            ->line('Service Type: ' . ucfirst($this->booking->type))
            ->line('Check-in Date: ' . $this->booking->check_in_date->format('M d, Y'));

        if ($this->booking->type === 'hotel') {
            $message->line('Check-out Date: ' . $this->booking->check_out_date->format('M d, Y'))
                ->line('Total Days: ' . $this->booking->total_days)
                ->line('Room Type: ' . $this->booking->roomType->name);
        } elseif ($this->booking->type === 'spa') {
            $message->line('Appointment Time: ' . $this->booking->spaBooking->appointment_time->format('h:i A'))
                ->line('Service: ' . $this->booking->spaBooking->spaPackage->name);
        } elseif ($this->booking->type === 'spay') {
            $message->line('Pet Name: ' . $this->booking->spayBooking->pet_name)
                ->line('Procedure: ' . $this->booking->spayBooking->spayPackage->name);
        }

        return $message->line('Total Amount: $' . number_format($this->booking->final_amount, 2))
            ->line('Please ensure you bring the required documents on your visit.')
            ->action('View Booking Details', url('/bookings/' . $this->booking->booking_number))
            ->line('Thank you for choosing Pawtel!');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'type' => $this->booking->type,
            'message' => 'Your booking has been confirmed',
            'amount' => $this->booking->final_amount,
        ];
    }
}
