<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'image',
        'created_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    // Add accessor to get full image URL
    public function getImageAttribute($value)
    {
        return $value ? asset($value) : null;
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}




