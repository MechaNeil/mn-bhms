<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Property;
use App\Models\Gender;
use App\Models\Status;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'property_id' => Property::inRandomOrder()->first()->id,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'profile_picture' => '/empty-user.jpg',
            'proof_of_identity' => '/empty-user.jpg',
            'status_id' => $this->faker->randomElement([1, 2]), // Randomly assign status_id as 1 or 2
        ];
    }
}
