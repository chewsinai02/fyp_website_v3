<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAssignment extends Model
{
    protected $fillable = [
        'nurse_id',
        'room_id',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'active' => 'success',
            'completed' => 'secondary',
            'cancelled' => 'danger'
        ][$this->status] ?? 'secondary';
    }
} 