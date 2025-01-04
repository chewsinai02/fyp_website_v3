<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'staff_id',
        'gender',
        'ic_number',
        'address',
        'blood_type',
        'contact_number',
        'medical_history',
        'description',
        'emergency_contact',
        'relation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'medical_history' => 'array',
    ];

    protected $appends = ['profile_picture_url'];

    public function getProfilePictureAttribute($value)
    {
        return $value ? $value : 'images/profile.png';
    }

    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture && Storage::disk('public')->exists($this->profile_picture)) {
            return Storage::url($this->profile_picture);
        }
        return asset('images/profile.png');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'patient_id');
    }

    public function getAgeFromIc()
    {
        $icNumber = $this->ic_number;
        $dobString = substr($icNumber, 0, 6);
        
        $year = substr($dobString, 0, 2);
        $currentYear = date('y');
        $fullYear = (int)$year > (int)$currentYear ? "19$year" : "20$year";
        
        $month = substr($dobString, 2, 2);
        $day = substr($dobString, 4, 2);
        
        $dob = \Carbon\Carbon::createFromFormat('Y-m-d', "$fullYear-$month-$day");
        return $dob->age;
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function roomAssignments()
    {
        return $this->hasMany(RoomAssignment::class, 'nurse_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'nurse_id');
    }

    public function getCurrentRoom()
    {
        return $this->schedules()
            ->with('room')
            ->whereDate('date', today())
            ->first()?->room;
    }

    public function isNurse()
    {
        return $this->role === 'nurse';
    }

    public function activeRoomAssignments()
    {
        return $this->roomAssignments()->where('status', 'active');
    }

    public function todaySchedule()
    {
        return $this->schedules()
            ->whereDate('date', today())
            ->orderBy('shift');
    }

    // Add relationship to Bed model
    public function bed()
    {
        return $this->hasOne(Bed::class, 'patient_id');
    }

    public function beds()
    {
        return $this->hasMany(Bed::class, 'patient_id');
    }

    public function scopeNurse($query)
    {
        return $query->where('role', 'nurse');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'user_id');
    }

    // Add condition color helper
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

    /**
     * Get the vital signs for the user (when they are a patient)
     */
    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class, 'patient_id');
    }

    /**
     * Get the vital signs recorded by the user (when they are a nurse)
     */
    public function recordedVitalSigns()
    {
        return $this->hasMany(VitalSign::class, 'nurse_id');
    }

    /**
     * Get the user's vital signs.
     */
    public function vital_signs()
    {
        return $this->hasMany(VitalSign::class, 'patient_id');
    }

    public function nurseSchedules()
    {
        return $this->hasMany(NurseSchedule::class, 'nurse_id');
    }

    public function assignedRooms()
    {
        return $this->belongsToMany(Room::class, 'nurse_schedules', 'nurse_id', 'room_id')
                    ->whereDate('date', today());
    }

    public function nurseSchedule()
    {
        return $this->hasMany(NurseSchedule::class, 'nurse_id');
    }

    public function getTodayScheduleStatus()
    {
        // Check if nurse has a schedule for today
        $hasSchedule = $this->nurseSchedule()
            ->whereDate('date', today())
            ->exists();
        
        // If schedule exists for today = On Duty, otherwise = Off Duty
        return $hasSchedule ? 'On Duty' : 'Off Duty';
    }

    /**
     * Check if the user has any of the given roles
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        // Convert user's role to lowercase
        $userRole = strtolower($this->role);
        
        if (is_string($roles)) {
            return $userRole === strtolower($roles);
        }
        
        // Convert all roles to lowercase for comparison
        $roles = array_map('strtolower', (array) $roles);
        return in_array($userRole, $roles);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }
}