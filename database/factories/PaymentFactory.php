<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'invoice_id' => Invoice::inRandomOrder()->first()?->id ?? 1,
            'payment_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'amount_paid' => $this->faker->randomFloat(2, 100, 5000),
            'proof' => $this->faker->optional()->imageUrl(640, 480, 'business', true, 'Proof'),
            'payment_method_id' => PaymentMethod::inRandomOrder()->first()?->id ?? 1,
        ];
    }
}
