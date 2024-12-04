<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'patient_id',
        'room_id'
    ];

    protected $casts = [
        'due_date' => 'datetime'
    ];

    protected $dates = [
        'due_date',
        'created_at',
        'updated_at'
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
} 