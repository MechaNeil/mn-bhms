<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition()
    {
        $names = ['Cash', 'Bank Transfer', 'Gcash', 'Paymaya', 'Credit Card'];
        $name = $this->faker->unique()->randomElement($names);
        return [
            'name' => $name,
            'payment_logo' => strtolower(str_replace(' ', '_', $name)) . '.png',
            'description' => $this->faker->sentence(),
        ];
    }
}
