<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'status', 'notes'
    ];

    protected $with = ['patient'];

    // Relationship to the User model for patients
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Relationship to the User model for doctors
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'patient_id', 'patient_id');
    }
}
