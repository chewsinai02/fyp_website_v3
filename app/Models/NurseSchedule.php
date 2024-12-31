<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseSchedule extends Model
{
    protected $fillable = ['nurse_id', 'room_id', 'date', 'shift'];
    
    protected $dates = ['date'];
    
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
    
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'scheduled' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'in_progress' => 'warning',
            default => 'secondary'
        };
    }

    public function getShiftColorAttribute()
    {
        return match($this->shift) {
            'morning' => 'info',
            'afternoon' => 'warning',
            'night' => 'dark',
            default => 'secondary'
        };
    }
} 