<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected static $roomNumber = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::inRandomOrder()->first()->id,
            'room_no' => 'RM-' . str_pad(self::$roomNumber++, 4, '0', STR_PAD_LEFT), // Format room number
            'room_image' => $this->faker->imageUrl(300, 300, 'room'),
            'room_description' => $this->faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
