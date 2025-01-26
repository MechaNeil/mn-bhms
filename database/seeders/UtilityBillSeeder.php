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
            'property_id' => 1,
            'utility_type_id' => 1,
            'amount' => 5000.00,
            'date_issued' => now()->format('Ymd'),

        ]);

        UtilityBill::create([
            'property_id' => 1,
            'utility_type_id' => 2,
            'amount' => 1000.00,
            'date_issued' => now()->format('Ymd'),

        ]);

    }
}
