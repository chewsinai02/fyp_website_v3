<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_number',
        'floor',
        'type',
        'total_beds',
        'notes'
    ];

    protected static function boot()
    {
        parent::boot();

        // When deleting a room, delete all associated beds
        static::deleting(function($room) {
            $room->beds()->delete();
        });
    }

    /**
     * Get the beds for the room
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get the assignments for the room
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class);
    }

    /**
     * Get the schedules for the room
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(NurseSchedule::class);
    }

    /**
     * Get the room type label
     */
    public function getTypeNameAttribute(): string
    {
        return [
            'ward' => 'Ward',
            'private' => 'Private Room',
            'icu' => 'ICU'
        ][$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get the status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return [
            'available' => 'success',
            'full' => 'warning',
            'maintenance' => 'warning',
            'closed' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    public function getCurrentNursesForShift($date, $shift)
    {
        return $this->schedules()
            ->whereDate('date', $date)
            ->where('shift', $shift)
            ->count();
    }

    public function hasAvailableSlot($date, $shift)
    {
        return $this->getCurrentNursesForShift($date, $shift) < $this->max_nurses_per_shift;
    }

    public function getAvailableBedsCountAttribute()
    {
        return $this->beds()->where('status', 'available')->count();
    }

    public function getAvailableBedsAttribute()
    {
        return $this->beds()
                   ->where('status', 'available')
                   ->count();
    }

    public function getStatusAttribute()
    {
        $availableBeds = $this->getAvailableBedsAttribute();
        return $availableBeds === 0 ? 'full' : 'available';
    }
} 