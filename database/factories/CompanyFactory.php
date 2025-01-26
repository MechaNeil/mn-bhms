<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usersWithRole2 = User::factory()->count(1)->create(['role_id' => 2]);
        return [
            'user_id' => $usersWithRole2->random()->id, // Assign user_id randomly from users with role_id 2
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'website' => $this->faker->url,
            'logo' => $this->faker->imageUrl(100, 100, 'business'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
