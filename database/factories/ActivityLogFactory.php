<?php

namespace Database\Factories;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Generates a user if one doesn't exist
            'property_id' => Property::factory(), // Generates a property if one doesn't exist
            'activity' => $this->faker->sentence(), // Generates a random activity description
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'), // Generates a random date within the last year
        ];
    }
}