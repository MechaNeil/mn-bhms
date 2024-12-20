<?php

namespace Database\Factories;
use App\Models\Property;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(), // Generates a company if one doesn't exist
            'property_name' => $this->faker->company . ' Apartments',
            'address' => $this->faker->address,
            'contact_no' => $this->faker->phoneNumber,
        ];
    }
}