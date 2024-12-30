<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Property;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_no' => $this->faker->unique()->numerify('INV-####'),
            'date_issued' => $this->faker->date(),
            'due_date' => $this->faker->date('+1 month'),
            'status_id' => Status::factory(),
            'remarks' => $this->faker->sentence(),
            'amount_paid' => $this->faker->randomFloat(2, 0, 1000),
            'penalty_amount' => $this->faker->randomFloat(2, 0, 100),
            'discount_amount' => $this->faker->randomFloat(2, 0, 100),
            'tenant_id' => Tenant::factory(),
            'property_id' => Property::factory(),
            'room_id' => Room::factory(),
            'user_id' => User::factory(),
            'utility_bills' => $this->faker->randomFloat(2, 0, 500),
            'constant_utility_bills' => $this->faker->randomFloat(2, 0, 200),
        ];
    }
}
