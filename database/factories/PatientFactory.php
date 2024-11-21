<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PatientFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        return [
            'name' => $this->faker->name($gender),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('123456789'),
            'role' => 'patient',
            'staff_id' => null,
            'gender' => $gender,
            'ic_number' => $this->generateMalaysianIC(),
            'address' => $this->faker->address,
            'blood_type' => $this->faker->randomElement(['rh+ a', 'rh- a', 'rh+ b', 'rh- b', 'rh+ ab', 'rh- ab', 'rh+ o', 'rh- o']),
            'contact_number' => $this->faker->phoneNumber,
            'medical_history' => $this->faker->randomElement(['None', 'High Blood Pressure', 'Diabetes', 'Asthma', 'None']),
            'description' => $this->faker->optional()->paragraph(),
            'emergency_contact' => $this->faker->phoneNumber,
            'relation' => $this->faker->randomElement(['Father', 'Mother', 'Sibling']),
            'profile_picture' => 'images/profile.png',
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate a valid format Malaysian IC number
     * Format: YYMMDD-PB-####
     * YY = Year (00-99)
     * MM = Month (01-12)
     * DD = Day (01-31)
     * PB = Place of birth (01-59)
     * #### = Random numbers
     */
    protected function generateMalaysianIC()
    {
        // Generate birth date components (between 1950 and 2005)
        $year = str_pad(rand(50, 05), 2, '0', STR_PAD_LEFT);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT); // Using 28 to be safe

        // Generate place of birth (01-59)
        $pb = str_pad(rand(1, 59), 2, '0', STR_PAD_LEFT);

        // Generate last 4 random digits
        $lastFour = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Combine all parts
        return $year . $month . $day . $pb . $lastFour;
    }
} 