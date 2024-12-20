<?php

namespace Database\Factories;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['Tenant', 'Assistant'];

        // Return the predefined roles only for Tenant and Assistant
        return [
            'role_name' => $this->faker->randomElement($roles),
            'description' => $this->faker->sentence,
        ];
    }
    public function createAdmin()
    {
        return Role::create([
            'role_name' => 'Admin',
            'description' => 'Administrator with full permissions.',
        ]);
    }
}
