<?php

namespace Database\Seeders;

use App\Models\Hotels;
use App\Models\Rooms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $hotels = Hotels::all();

        foreach ($hotels as $hotel) {
            for ($i = 0; $i < 5; $i++) {
                Rooms::create([
                    'hotel_id' => $hotel->id,
                    'name' => $faker->randomElement(['Standart', 'Deluxe', 'Suite', 'Family', 'Presidential']) . ' Room ' . ($i + 1),
                    'description' => $faker->paragraphs(2, true),
                    'price' => $faker->numberBetween(100, 1000),
                    'capacity' => $faker->numberBetween(1, 6),
                    'is_available' => $faker->boolean(80), // 80% chance of being available
                ]);
            }
        }
    }
}
