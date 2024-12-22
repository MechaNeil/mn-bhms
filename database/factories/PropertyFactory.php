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
    protected static $apartmentNumber = 1;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {

        return [
            'company_id' => Company::inRandomOrder()->first(), // Generates a company if one doesn't exist
            'image' => $this->faker->imageUrl(100, 100, 'building'),
            'apartment_no' => 'AP-' . str_pad(self::$apartmentNumber++, 4, '0', STR_PAD_LEFT), // Format apartment number            
            'name' => $this->faker->company . ' Apartments',
            'address' => $this->faker->address,
            'contact_no' => $this->faker->phoneNumber,
        ];
    }
}