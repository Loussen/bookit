<?php

namespace Database\Seeders;

use App\Models\RoomPhotos;
use App\Models\Rooms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RoomPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $rooms = Rooms::all();

        foreach ($rooms as $room) {
            for ($i = 0; $i < 5; $i++) {
                RoomPhotos::create([
                    'room_id' => $room->id,
                    'photo_url' => $faker->imageUrl(800, 600, 'room'),
                ]);
            }
        }
    }
}
