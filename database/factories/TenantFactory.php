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
     * @param int $randomValue
     * @return string
     */
    private function generateUniqueUsername(string $firstName, string $lastName, int $randomValue): string
    {
        return strtolower($firstName . '.' . $lastName . $randomValue);
    }

    /**
     * Generate a unique email using first name and last name.
     *
     * @param string $firstName
     * @param string $lastName
     * @param int $randomValue
     * @return string
     */
    private function generateUniqueEmail(string $firstName, string $lastName, int $randomValue): string
    {
        return strtolower($firstName . '.' . $lastName . $randomValue . '@example.com');
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
        $randomValue = rand(1, 1000);

        // Create the user associated with the tenant
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'username' => $this->generateUniqueUsername($firstName, $lastName, $randomValue),
            'email' => $this->generateUniqueEmail($firstName, $lastName, $randomValue),
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'address' => $this->faker->address,
            'contact_no' => $this->faker->phoneNumber,
            'password' => Hash::make('password'), // Default password
            'avatar' => '/empty-user.jpg',
            // role status
            'role_id' => 4,
            'status_id' => $this->faker->randomElement([1, 2]), // Randomly assign status_id as 1 or 2
        ]);

        return [
            'user_id' => $user->id,
            'document_type' => 'Id',
            'document_url' => '/empty-user.jpg',

        ];
    }
}
