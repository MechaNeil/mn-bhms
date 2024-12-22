<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;
use Carbon\Carbon;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // Tenant Status
            [
                'name' => 'Active',
                'description' => 'The tenant is currently active.',
                'context' => 'Tenant Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Inactive',
                'description' => 'The tenant is currently inactive.',
                'context' => 'Tenant Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pending',
                'description' => 'The tenant\'s status is pending.',
                'context' => 'Tenant Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // User Status
            [
                'name' => 'Active',
                'description' => 'The user is currently active.',
                'context' => 'User Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Inactive',
                'description' => 'The user is currently inactive.',
                'context' => 'User Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Room Status
            [
                'name' => 'Occupied',
                'description' => 'The room is currently occupied.',
                'context' => 'Room Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Vacant',
                'description' => 'The room is currently vacant.',
                'context' => 'Room Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Bed Status
            [
                'name' => 'Occupied',
                'description' => 'The bed is currently occupied.',
                'context' => 'Bed Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Vacant',
                'description' => 'The bed is currently vacant.',
                'context' => 'Bed Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Payment Status
            [
                'name' => 'Paid',
                'description' => 'The payment has been made.',
                'context' => 'Payment Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pending',
                'description' => 'The payment is pending.',
                'context' => 'Payment Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Overdue',
                'description' => 'The payment is overdue.',
                'context' => 'Payment Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Activity Status
            [
                'name' => 'Active',
                'description' => 'The activity is currently active.',
                'context' => 'Activity Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Inactive',
                'description' => 'The activity is currently inactive.',
                'context' => 'Activity Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}