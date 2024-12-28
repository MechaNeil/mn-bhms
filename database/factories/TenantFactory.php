<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Property;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Tenant>
 */

class TenantFactory extends Factory
{
    protected static ?string $password;

    /**
     * Generate a unique username using first name and last name.
     *
     * @param string $firstName
     * @param string $lastName
     * @return string
     */
    private function generateUniqueUsername(string $firstName, string $lastName): string
    {
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

        // Create the user associated with the tenant
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'username' => $this->generateUniqueUsername($firstName, $lastName),
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // Default password
            'avatar' => '/empty-user.jpg',
            // role status
            'role_id' => 1,
            'status_id' => 1,
        ]);

        return [
            'user_id' => $user->id,
            'property_id' => Property::inRandomOrder()->first()->id,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'profile_picture' => '/empty-user.jpg',
            'proof_of_identity' => json_encode(['/empty-user.jpg']),
            'status_id' => $this->faker->randomElement([1, 2]), // Randomly assign status_id as 1 or 2
        ];
    }
}