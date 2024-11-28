<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    protected $fillable = [
        'patient_id',
        'nurse_id',
        'temperature',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate'
    ];

    /**
     * Get the patient that owns the vital signs.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the nurse who recorded the vital signs.
     */
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
}
