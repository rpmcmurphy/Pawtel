<?php
// app/Services/Admin/BookingManagementService.php
namespace App\Services\Admin;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Services\NotificationService;
use App\Services\PricingService;
use App\Services\BookingService;
use App\Services\AvailabilityService;
use Illuminate\Support\Facades\DB;

class BookingManagementService
{
    public function __construct(
        private BookingRepository $bookingRepo,
        private NotificationService $notificationService,
        private PricingService $pricingService,
        private BookingService $bookingService,
        private AvailabilityService $availabilityService
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
            $type = $data['type'];
            $userId = $data['user_id'];
            $checkInDate = $data['check_in_date'];
            $checkOutDate = $data['check_out_date'];
            $addons = $data['addons'] ?? [];
            
            // Check if user is resident (for spa/spay discounts)
            $isResident = $this->pricingService->isUserResident($userId, $checkInDate);
            
            // Calculate pricing based on booking type
            if ($type === 'hotel') {
                // Check availability
                if (!$this->availabilityService->checkHotelAvailability(
                    $data['room_type_id'],
                    $checkInDate,
                    $checkOutDate
                )) {
                    throw new \Exception('No vacancy available for the selected dates');
                }
                
                // Calculate pricing
                $pricing = $this->pricingService->calculateHotelBooking(
                    $data['room_type_id'],
                    $checkInDate,
                    $checkOutDate,
                    $addons,
                    $data['custom_monthly_discount'] ?? null
                );
                
                // Use calculated price or allow admin override
                $finalAmount = $data['final_amount'] ?? $pricing['final_amount'];
                $totalAmount = $data['total_amount'] ?? $pricing['total_amount'];
                $discountAmount = $totalAmount - $finalAmount;
                
                // Create hotel booking
                $booking = $this->bookingService->createHotelBooking([
                    'user_id' => $userId,
                    'type' => 'hotel',
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'room_type_id' => $data['room_type_id'],
                    'total_days' => $pricing['total_days'],
                    'total_amount' => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'special_requests' => $data['special_requests'] ?? null,
                    'is_resident' => $isResident,
                    'addons' => $addons,
                ]);
                
            } elseif ($type === 'spa') {
                // Check availability
                if (!$this->availabilityService->checkSpaAvailability(
                    $data['spa_package_id'],
                    $checkInDate,
                    $data['appointment_time'] ?? '09:00'
                )) {
                    throw new \Exception('No slots available for the selected date and time');
                }
                
                // Calculate pricing
                $pricing = $this->pricingService->calculateSpaBooking(
                    $data['spa_package_id'],
                    $addons,
                    $isResident
                );
                
                // Use calculated price or allow admin override
                $finalAmount = $data['final_amount'] ?? $pricing['final_amount'];
                $totalAmount = $data['total_amount'] ?? $pricing['total_amount'];
                $discountAmount = $totalAmount - $finalAmount;
                
                // Create spa booking
                $booking = $this->bookingService->createSpaBooking([
                    'user_id' => $userId,
                    'type' => 'spa',
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkInDate,
                    'total_days' => 1,
                    'total_amount' => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'special_requests' => $data['special_requests'] ?? null,
                    'is_resident' => $isResident,
                    'addons' => $addons,
                    'spa_details' => [
                        'spa_package_id' => $data['spa_package_id'],
                        'appointment_date' => $checkInDate,
                        'appointment_time' => $data['appointment_time'] ?? '09:00',
                        'notes' => $data['notes'] ?? null,
                    ]
                ]);
                
            } elseif ($type === 'spay') {
                // Check availability
                if (!$this->availabilityService->checkSpayAvailability(
                    $data['spay_package_id'],
                    $checkInDate
                )) {
                    throw new \Exception('No slots available for the selected date');
                }
                
                // Get post-care days
                $spayPackage = \App\Models\SpayPackage::find($data['spay_package_id']);
                $postCareDays = $data['post_care_days'] ?? $spayPackage->post_care_days ?? 0;
                
                // Calculate pricing
                $pricing = $this->pricingService->calculateSpayBooking(
                    $data['spay_package_id'],
                    $addons,
                    $isResident,
                    $postCareDays
                );
                
                // Use calculated price or allow admin override
                $finalAmount = $data['final_amount'] ?? $pricing['final_amount'];
                $totalAmount = $data['total_amount'] ?? $pricing['total_amount'];
                $discountAmount = $totalAmount - $finalAmount;
                
                // Create spay booking
                $booking = $this->bookingService->createSpayBooking([
                    'user_id' => $userId,
                    'type' => 'spay',
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkInDate,
                    'total_days' => 1,
                    'total_amount' => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'special_requests' => $data['special_requests'] ?? null,
                    'is_resident' => $isResident,
                    'addons' => $addons,
                    'spay_details' => [
                        'spay_package_id' => $data['spay_package_id'],
                        'procedure_date' => $checkInDate,
                        'pet_name' => $data['pet_name'] ?? 'Pet',
                        'pet_age' => $data['pet_age'] ?? null,
                        'pet_weight' => $data['pet_weight'] ?? null,
                        'medical_notes' => $data['medical_notes'] ?? null,
                    ]
                ]);
            } else {
                throw new \Exception('Invalid booking type');
            }
            
            // Mark as manual entry and set reference
            $booking->update([
                'is_manual_entry' => true,
                'manual_reference' => $data['manual_reference'],
                'status' => $data['status'] ?? 'confirmed',
                'confirmed_at' => ($data['status'] ?? 'confirmed') === 'confirmed' ? now() : null,
            ]);
            
            // Send confirmation email if requested
            if (isset($data['send_confirmation']) && $data['send_confirmation']) {
                $this->notificationService->sendBookingConfirmation($booking);
            }
            
            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateBooking(int $bookingId, array $data): Booking
    {
        $booking = $this->bookingRepo->findOrFail($bookingId);
        
        $updateData = [];
        
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
            
            // Set timestamps based on status
            if ($data['status'] === 'confirmed' && !$booking->confirmed_at) {
                $updateData['confirmed_at'] = now();
            }
            if ($data['status'] === 'cancelled' && !$booking->cancelled_at) {
                $updateData['cancelled_at'] = now();
            }
        }
        
        if (isset($data['special_requests'])) {
            $updateData['special_requests'] = $data['special_requests'];
        }
        
        if (isset($data['final_amount'])) {
            $updateData['final_amount'] = $data['final_amount'];
            // Recalculate discount if total_amount exists
            if ($booking->total_amount) {
                $updateData['discount_amount'] = $booking->total_amount - $data['final_amount'];
            }
        }
        
        return $this->bookingRepo->update($bookingId, $updateData);
    }
}
