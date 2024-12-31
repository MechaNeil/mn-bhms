<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UtilityBill;

class UtilityBillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UtilityBill::create([
            'rate' => 0.15,
            'name' => 'Electricity'
        ]);

        UtilityBill::create([
            'rate' => 0.10,
            'name' => 'Water'
        ]);

        UtilityBill::create([
            'rate' => 0.05,
            'name' => 'Gas'
        ]);
    }
}
