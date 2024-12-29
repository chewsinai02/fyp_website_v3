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
} 