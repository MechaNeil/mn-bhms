<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'company_name' => $this->faker->company,
            'address' => $this->faker->address,
            'contact_no' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'company_logo' => $this->faker->imageUrl(100, 100, 'business'),
        ];
    }
}
