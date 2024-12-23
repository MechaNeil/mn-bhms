<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AssistantFactory extends Factory
{
    protected static ?string $password;
    /**
     * Generate a unique username using first name and last name.
     *
     * @param string $firstName
     * @param string $lastName
     * @return string
     */
    private function generateUniqueUsername(string $firstName, string $lastName): string {
        return strtolower($firstName . '.' . $lastName . rand(1, 1000));
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'username' => $this->generateUniqueUsername($firstName, $lastName),
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // Default password
            'avatar' => '/empty-user.jpg',
            'role_id' => 2,
            'status_id' => 1,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
