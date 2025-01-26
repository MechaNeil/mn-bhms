<?php

namespace Database\Factories;

use App\Models\Assistant;
use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assistant>
 */
class AssistantFactory extends Factory
{
    protected $model = Assistant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usersWithRole3 = User::factory()->count(1)->create(['role_id' => 3]);
        return [
            'user_id' => $usersWithRole3->first()->id, // Assign user_id from the first user with role_id 3
            'property_id' => Property::inRandomOrder()->first()->id,
            
        ];
    }
}
