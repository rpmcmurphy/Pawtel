<?php
// app/Services/Admin/BookingManagementService.php
namespace App\Services\Admin;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class BookingManagementService
{
    public function __construct(
        private BookingRepository $bookingRepo,
        private NotificationService $notificationService
    ) {}

    public function confirmBooking(int $bookingId, array $data = []): Booking
    {
        $booking = $this->bookingRepo->findOrFail($bookingId);

        if ($booking->status !== 'pending') {
            throw new \Exception('Only pending bookings can be confirmed');
        }

        DB::beginTransaction();
        try {
            $booking = $this->bookingRepo->update($bookingId, [
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Assign rooms if provided
            if (!empty($data['room_assignments'])) {
                $booking->rooms()->sync($data['room_assignments']);
            }

            $this->notificationService->sendBookingConfirmation($booking);

            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancelBooking(int $bookingId, string $reason): Booking
    {
        $booking = $this->bookingRepo->findOrFail($bookingId);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            throw new \Exception('This booking cannot be cancelled');
        }

        $booking = $this->bookingRepo->update($bookingId, [
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        $this->notificationService->sendBookingCancellation($booking);

        return $booking;
    }

    public function createManualBooking(array $data): Booking
    {
        DB::beginTransaction();
        try {
            $totalDays = \Carbon\Carbon::parse($data['check_in_date'])
                ->diffInDays(\Carbon\Carbon::parse($data['check_out_date'])) + 1;

            $bookingData = array_merge($data, [
                'total_days' => $totalDays,
                'total_amount' => $data['final_amount'],
                'discount_amount' => 0,
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'is_manual_entry' => true,
            ]);

            $booking = $this->bookingRepo->create($bookingData);

            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
