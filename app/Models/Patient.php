<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Bed;
use App\Models\VitalSign;

class Patient extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'gender',
        'admission_date',
        'diagnosis',
        'condition'
    ];

    public function bed()
    {
        return $this->hasOne(Bed::class, 'patient_id', 'id');
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

    public function scopePatients($query)
    {
        return $query->where('role', 'patient');
    }
} 