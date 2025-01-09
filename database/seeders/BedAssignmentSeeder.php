<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BedAssignment;

class BedAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        BedAssignment::factory()
            ->count(2)
            ->create()
            ->each(function ($bedAssignment) {
                $bedAssignment->createInvoices($bedAssignment);
            });
    }
}
