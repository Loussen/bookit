<?php

namespace Database\Seeders;

use App\Models\Hotels;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            Hotels::create([
                'name' => $faker->company . ' Hotel',
                'description' => $faker->paragraphs(3, true),
                'address' => $faker->address,
                'photo_url' => $faker->imageUrl(640, 480, 'hotel'),
                'location_lat' => $faker->latitude,
                'location_lon' => $faker->longitude,
                'rating' => $faker->randomFloat(1, 3.5, 5.0),
                'price_range' => $faker->randomElement(['$', '$$', '$$$', '$$$$', '$$$$$']),
            ]);
        }
    }
}
