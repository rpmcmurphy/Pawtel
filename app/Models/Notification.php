<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $keyType = 'string'; // because UUID
    public $incrementing = false; // UUID is not auto-increment

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // Mark as read
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    // Check if read
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }
}
