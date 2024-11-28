<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Bed;
use App\Models\VitalSign;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'date_of_birth',
        'gender',
        'admission_date',
        'diagnosis',
        'condition'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function vital_signs()
    {
        return $this->hasMany(VitalSign::class);
    }

    public function getConditionColorAttribute()
    {
        return match($this->condition) {
            'Critical' => 'danger',
            'Serious' => 'warning',
            'Fair' => 'info',
            'Good' => 'success',
            default => 'secondary'
        };
    }
} 