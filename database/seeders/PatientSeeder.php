<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Database\Factories\PatientFactory;

class PatientSeeder extends Seeder
{
    public function run()
    {
        // Create 20 patients with all required fields
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => bcrypt('123456789'),
                'role' => 'patient',
                'staff_id' => null,
                'gender' => fake()->randomElement(['male', 'female']),
                'ic_number' => $this->generateMalaysianIC(),
                'address' => fake()->address(),
                'blood_type' => fake()->randomElement(['rh+ a', 'rh- a', 'rh+ b', 'rh- b', 'rh+ ab', 'rh- ab', 'rh+ o', 'rh- o']),
                'contact_number' => fake()->phoneNumber(),
                'medical_history' => fake()->randomElement(['None', 'High Blood Pressure', 'Diabetes', 'Asthma', 'None']),
                'description' => fake()->optional()->paragraph(),
                'emergency_contact' => fake()->phoneNumber(),
                'relation' => fake()->randomElement(['Father', 'Mother', 'Sibling']),
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Generate a valid format Malaysian IC number
     */
    protected function generateMalaysianIC()
    {
        // Generate birth date components (between 1950 and 2005)
        $year = str_pad(rand(50, 05), 2, '0', STR_PAD_LEFT);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

        // Generate place of birth (01-59)
        $pb = str_pad(rand(1, 59), 2, '0', STR_PAD_LEFT);

        // Generate last 4 random digits
        $lastFour = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Combine all parts
        return $year . $month . $day . $pb . $lastFour;
    }
} 