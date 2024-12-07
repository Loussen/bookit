<?php

namespace Database\Seeders;

use App\Models\Reservations;
use App\Models\Rooms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReservationSeeder extends Seeder
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
                $checkIn = $faker->dateTimeBetween('now', '+2 months');
                $checkOut = $faker->dateTimeBetween($checkIn, $checkIn->format('Y-m-d H:i:s') . ' +2 weeks');

                Reservations::create([
                    'room_id' => $room->id,
                    'client_telegram_id' => (string)$faker->numberBetween(100000000, 999999999),
                    'client_name' => $faker->name,
                    'client_phone' => $faker->phoneNumber,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guest_count' => $faker->numberBetween(1, 6),
                    'total_price' => $faker->numberBetween(200, 2000),
                    'status' => $faker->randomElement(['pending', 'approved', 'paid', 'rejected', 'cancelled']),
                    'notes' => $faker->optional(0.7)->sentence, // 70% chance of having notes
                ]);
            }
        }
    }
}
