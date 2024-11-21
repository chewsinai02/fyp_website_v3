<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Bed;

class RoomSeeder extends Seeder
{
    public function run()
    {
        // Create some test rooms
        $rooms = [
            [
                'room_number' => '101',
                'floor' => 1,
                'type' => 'ward',
                'total_beds' => 4,
            ],
            [
                'room_number' => '102',
                'floor' => 1,
                'type' => 'private',
                'total_beds' => 1,
            ],
            [
                'room_number' => '201',
                'floor' => 2,
                'type' => 'icu',
                'total_beds' => 2,
            ],
        ];

        foreach ($rooms as $roomData) {
            $room = Room::create($roomData);
            
            // Create beds for each room
            for ($i = 1; $i <= $roomData['total_beds']; $i++) {
                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => $i,
                    'status' => 'available'
                ]);
            }
        }
    }
} 