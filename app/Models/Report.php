<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'title',
        'report_date',
        'description',
        'diagnosis',
        'treatment_plan',
        'symptoms',
        'examination_findings',
        'lab_results',
        'medications',
        'medical_history',
        'follow_up_instructions',
        'follow_up_date',
        'weight',
        'height',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'notes',
        'attachments',
        'status'
    ];

    protected $casts = [
        'attachments' => 'array',
        'medical_history' => 'array',
        'report_date' => 'date',
        'follow_up_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'temperature' => 'decimal:1',
    ];

    protected $with = ['patient'];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'patient_id', 'patient_id');
    }
} 