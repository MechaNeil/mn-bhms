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

            // User Status 1
            [
                'name' => 'Active',
                'context' => 'User Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 2
            [
                'name' => 'Inactive',
                'context' => 'User Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 3
            [
                'name' => 'Pending',
                'context' => 'User Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 4
            [
                'name' => 'Suspended',
                'context' => 'User Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 5
            [
                'name' => 'Deleted',
                'context' => 'User Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Room Status 6
            [
                'name' => 'Occupied',
                'context' => 'Room Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 7
            [
                'name' => 'Vacant',
                'context' => 'Room Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Bed Status 8
            [
                'name' => 'Occupied',
                'context' => 'Bed Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 9
            [
                'name' => 'Vacant',
                'context' => 'Bed Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Payment Status 10
            [
                'name' => 'Paid',
                'context' => 'Payment Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 11
            [
                'name' => 'Pending',
                'context' => 'Payment Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 12
            [
                'name' => 'Overdue',
                'context' => 'Payment Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Activity Status 13
            [
                'name' => 'Active',
                'context' => 'Activity Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 14
            [
                'name' => 'Inactive',
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