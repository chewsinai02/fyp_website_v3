<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NurseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'nurse_id',
        'room_id',
        'date',
        'shift',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    // Relationship to User model
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id')->withDefault([
            'name' => 'Unassigned Nurse'
        ]);
    }

    // Relationship to Room model
    public function room()
    {
        return $this->belongsTo(Room::class)->withDefault([
            'room_number' => 'No Room'
        ]);
    }

    // Status color helper
    public function getStatusColorAttribute()
    {
        return [
            'scheduled' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    // Shift time helper
    public function getShiftTimeAttribute()
    {
        return [
            'morning' => '7:00 AM - 3:00 PM',
            'evening' => '3:00 PM - 11:00 PM',
            'night' => '11:00 PM - 7:00 AM'
        ][$this->shift] ?? '';
    }

    public function getShiftColorAttribute()
    {
        return [
            'morning' => 'success',
            'afternoon' => 'warning',
            'night' => 'info'
        ][$this->shift] ?? 'secondary';
    }
} 