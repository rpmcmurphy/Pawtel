<?php

namespace App\Services\Web;

class BookingService extends ApiService
{
    // Hotel Booking Methods
    public function getRoomTypes()
    {
        return $this->get('availability/room-types');
    }

    public function checkAvailability($params)
    {
        return $this->post('availability/check', $params);
    }

    public function createHotelBooking($bookingData)
    {
        return $this->post('bookings/hotel', $bookingData);
    }

    // Spa Booking Methods
    public function getSpaPackages()
    {
        return $this->get('spa/packages');
    }

    public function checkSpaAvailability($params)
    {
        return $this->post('spa/availability', $params);
    }

    public function createSpaBooking($bookingData)
    {
        return $this->post('bookings/spa', $bookingData);
    }

    // Spay/Neuter Booking Methods
    public function getSpayPackages()
    {
        return $this->get('spay/packages');
    }

    public function checkSpayAvailability($params)
    {
        return $this->post('spay/availability', $params);
    }

    public function createSpayBooking($bookingData)
    {
        return $this->post('bookings/spay', $bookingData);
    }

    // General Booking Methods
    public function getBookings($params = [])
    {
        return $this->get('bookings', $params);
    }

    public function getBooking($id)
    {
        return $this->get("bookings/{$id}");
    }

    public function cancelBooking($id, $reason = null)
    {
        return $this->delete("bookings/{$id}/cancel", ['reason' => $reason]);
    }

    public function uploadBookingDocument($bookingId, $file, $documentType)
    {
        return $this->upload(
            "bookings/{$bookingId}/documents",
            ['document' => $file],
            ['document_type' => $documentType]
        );
    }

    public function getAddonServices($category = null)
    {
        $params = $category ? ['category' => $category] : [];
        return $this->get('addon-services', $params);
    }
}
