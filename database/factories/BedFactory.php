<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;
use App\Models\Status;
use App\Models\Room;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bed>
 */
class BedFactory extends Factory
{
    protected static $bedNumber = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::inRandomOrder()->first()->id,
            'bed_no' => 'BD-' . str_pad(self::$bedNumber++, 4, '0', STR_PAD_LEFT), // Format bed number            
            'monthly_rate' => $this->faker->randomFloat(2, 500, 5000),
            'status_id' =>  $this->faker->randomElement([8, 9]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
