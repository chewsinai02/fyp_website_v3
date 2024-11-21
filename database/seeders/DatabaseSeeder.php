<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'role' => 'admin',
                'staff_id' => 'a000001',
                'gender' => 'female',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456789'),
                'ic_number' => '021007019999',
                'address' => '123 Medical Street, Health City',
                'blood_type' => 'A+',
                'contact_number' => '0177777777',
                'medical_history' => 'allergies',
                'description' => 'Experienced in cardiology.',
                'emergency_contact' => '0188888888',
                'relation' => 'Mother',
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin1',
                'role' => 'admin',
                'staff_id' => 'a000002',
                'gender' => 'female',
                'email' => 'admin1@gmail.com',
                'password' => Hash::make('123456789'),
                'ic_number' => '021007019999',
                'address' => '123 Medical Street, Health City',
                'blood_type' => 'A+',
                'contact_number' => '0177777777',
                'medical_history' => 'allergies',
                'description' => 'Experienced in cardiology.',
                'emergency_contact' => '0188888888',
                'relation' => 'Mother',
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin2',
                'role' => 'admin',
                'staff_id' => 'a000003',
                'gender' => 'male',
                'email' => 'admin2@gmail.com',
                'password' => Hash::make('987654321'),
                'ic_number' => '031008019999',
                'address' => '456 Wellness Road, Health City',
                'blood_type' => 'B+',
                'contact_number' => '0178888888',
                'medical_history' => 'none',
                'description' => 'Experienced in general administration.',
                'emergency_contact' => '0199999999',
                'relation' => 'Father',
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'doctor',
                'role' => 'doctor',
                'staff_id' => 'd000001',
                'gender' => 'male',
                'email' => 'doctor@gmail.com',
                'password' => Hash::make('987654321'),
                'ic_number' => '031008019999',
                'address' => '456 Wellness Road, Health City',
                'blood_type' => 'rh- a',
                'contact_number' => '0178888888',
                'medical_history' => 'none',
                'description' => 'Experienced in general administration.',
                'emergency_contact' => '0199999999',
                'relation' => 'Father',
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'nurse_admin',
                'role' => 'nurse_admin',
                'staff_id' => 'na000001',
                'gender' => 'male',
                'email' => 'nurseadmin@gmail.com',
                'password' => Hash::make('987654321'),
                'ic_number' => '031008019999',
                'address' => '456 Wellness Road, Health City',
                'blood_type' => 'rh- a',
                'contact_number' => '0178888888',
                'medical_history' => 'none',
                'description' => 'Experienced in general administration.',
                'emergency_contact' => '0199999999',
                'relation' => 'Father',
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'nurse',
                'role' => 'nurse',
                'staff_id' => 'n000001',
                'gender' => 'male',
                'email' => 'nurse@gmail.com',
                'password' => Hash::make('987654321'),
                'ic_number' => '031008019999',
                'address' => '456 Wellness Road, Health City',
                'blood_type' => 'rh- a',
                'contact_number' => '0178888888',
                'medical_history' => 'none',
                'description' => 'Experienced in general administration.',
                'emergency_contact' => '0199999999',
                'relation' => 'Father',
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'patient',
                'role' => 'patient',
                'staff_id' => null,
                'gender' => 'male',
                'email' => 'patient@gmail.com',
                'password' => Hash::make('987654321'),
                'ic_number' => '031008019999',
                'address' => '456 Wellness Road, Health City',
                'blood_type' => 'rh- a',
                'contact_number' => '0178888888',
                'medical_history' => 'none',
                'description' => 'Experienced in general administration.',
                'emergency_contact' => '0199999999',
                'relation' => 'Father',
                'profile_picture' => 'images/profile.png',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('messages')->insert([
            [
                'sender_id' => '7',
                'receiver_id' => '4',
                'message' => 'Hi, Dr Chew',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->call([
            RoomSeeder::class,
            PatientSeeder::class,
        ]);
    }
}
