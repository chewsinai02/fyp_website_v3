<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bed extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'bed_number',
        'patient_id',
        'status'
    ];

    const STATUSES = ['available', 'occupied', 'maintenance'];

    protected static function boot()
    {
        parent::boot();

        // Before saving, update status based on patient_id
        static::saving(function ($bed) {
            // If patient_id is being changed
            if ($bed->isDirty('patient_id')) {
                // If patient_id is set and status isn't maintenance
                if ($bed->patient_id !== null && $bed->status !== 'maintenance') {
                    $bed->status = 'occupied';
                }
                // If patient_id is null and status is occupied
                elseif ($bed->patient_id === null && $bed->status === 'occupied') {
                    $bed->status = 'available';
                }
            }
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id')->where('role', 'patient');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    public function isInMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Get count of occupied beds
     */
    public static function getOccupiedBedsCount()
    {
        return self::whereNotNull('patient_id')->count();
    }

    /**
     * Get the color class based on the condition
     */
    public function getConditionColorAttribute()
    {
        return match ($this->condition) {
            'Critical' => 'danger',
            'Serious' => 'warning',
            'Fair' => 'info',
            'Good' => 'success',
            'Stable' => 'primary',
            default => 'secondary'
        };
    }

    public function vital_signs()
    {
        return $this->hasMany(VitalSign::class, 'patient_id', 'patient_id');
    }

    public function latest_vital_signs()
    {
        return $this->hasOne(VitalSign::class, 'patient_id', 'patient_id')
                    ->latest();
    }

    public function getLatestUpdateAttribute()
    {
        $vitalSignsUpdate = $this->vital_signs()
                                ->latest()
                                ->first()?->updated_at;
                                
        return $vitalSignsUpdate && $vitalSignsUpdate->gt($this->updated_at)
            ? $vitalSignsUpdate
            : $this->updated_at;
    }
} 