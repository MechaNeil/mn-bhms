<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{BedAssignment, Invoice, Status, Tenant, Property, Room, Bed, User};
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BedAssignment>
 */
class BedAssignmentFactory extends Factory
{
    protected $model = BedAssignment::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'room_id' => Room::factory(),
            'property_id' => Property::factory(),
            'bed_id' => Bed::factory(),
            'assigned_by' => User::factory(),
            'date_started' => $this->faker->date('Y-m-d', '+1 day'),
            'due_date' => $this->faker->date('Y-m-d', '+6 months'),
        ];
    }

    /**
     * Create invoices based on start_date and due_date.
     *
     * @param BedAssignment $bedAssignment
     * @return void
     */
    // public function createInvoices(BedAssignment $bedAssignment): void
    // {
    //     $startDate = Carbon::parse($bedAssignment->start_date->format('Y-m-d'));
    //     $dueDate = Carbon::parse($bedAssignment->due_date->format('Y-m-d'));

    //     $currentDate = $startDate->copy();
    //     $counter = 1;

    //     while ($currentDate->lessThanOrEqualTo($dueDate)) {
    //         Invoice::factory()->create([
    //             'invoice_no' => "INV-" . $bedAssignment->id . "-" . $counter,
    //             'date_issued' => $currentDate->copy()->startOfMonth(),
    //             'due_date' => $currentDate->copy()->endOfMonth(),
    //             'tenant_id' => $bedAssignment->tenant_id,
    //             'property_id' => $bedAssignment->property_id,
    //             'room_id' => $bedAssignment->room_id,
    //             'user_id' => $bedAssignment->assigned_by,
    //             'status_id' => Status::factory(),
    //             'amount_paid' => 0, // Default unpaid
    //         ]);

    //         $currentDate->addMonth(); // Move to the next month
    //         $counter++;
    //     }
    // }
}
