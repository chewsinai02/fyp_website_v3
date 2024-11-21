<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class NurseSeeder extends Seeder
{
    public function run()
    {
        // Start from 3
        $startNumber = 3;

        // Create 5 nurses with sequential IDs starting from N000003
        for ($i = 0; $i < 5; $i++) {
            $gender = fake()->randomElement(['male', 'female']);
            
            User::create([
                'name' => fake()->name($gender),
                'email' => fake()->unique()->safeEmail(),
                'password' => bcrypt('123456789'),
                'role' => 'nurse',
                'staff_id' => 'N' . str_pad($startNumber + $i, 6, '0', STR_PAD_LEFT), // Will start from N000003
                'gender' => $gender,
                'ic_number' => $this->generateMalaysianIC(),
                'address' => fake()->address(),
                'blood_type' => fake()->randomElement(['rh+ a', 'rh- a', 'rh+ b', 'rh- b', 'rh+ ab', 'rh- ab', 'rh+ o', 'rh- o']),
                'contact_number' => '01' . fake()->numberBetween(1, 9) . fake()->numerify('#######'),
                'medical_history' => fake()->randomElement(['None', 'High Blood Pressure', 'Diabetes', 'Asthma', 'None']),
                'description' => fake()->randomElement([
                    'Experienced in emergency care',
                    'Specialized in pediatric nursing',
                    'Expert in critical care',
                    'Skilled in geriatric care',
                    'Specialized in wound care'
                ]),
                'emergency_contact' => '01' . fake()->numberBetween(1, 9) . fake()->numerify('#######'),
                'relation' => fake()->randomElement(['Father', 'Mother', 'Spouse', 'Sibling']),
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function generateMalaysianIC()
    {
        $year = str_pad(rand(50, 99), 2, '0', STR_PAD_LEFT);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        $icNumber = $year . $month . $day . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $checksum = 0;
        for ($i = 0; $i < 11; $i++) {
            $checksum += $icNumber[$i] * (13 - $i);
        }
        $checksum = 11 - ($checksum % 11);
        if ($checksum == 10) {
            $checksum = 0;
        }
        if ($checksum == 11) {
            $checksum = 1;
        }
        $icNumber .= $checksum;
        return $icNumber;
    }
} 