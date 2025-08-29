<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'cat_name',
        'age',
        'gender',
        'breed',
        'health_status',
        'adoption_fee',
        'contact_info',
        'status',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'adoption_fee' => 'decimal:2',
    ];

    // Relationships
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAdopted(): bool
    {
        return $this->status === 'adopted';
    }
}
