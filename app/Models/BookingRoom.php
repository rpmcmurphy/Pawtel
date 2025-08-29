<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingRoom extends Pivot
{
    protected $table = 'booking_rooms';

    protected $casts = [
        'assigned_at' => 'datetime',
    ];
}
