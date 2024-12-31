<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConstantUtilityBill;

class ConstantUtilityBillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConstantUtilityBill::create([
            'number_of_appliances' => 5,
            'cost' => 100.00
        ]);

        ConstantUtilityBill::create([
            'number_of_appliances' => 3,
            'cost' => 60.00
        ]);

        ConstantUtilityBill::create([
            'number_of_appliances' => 7,
            'cost' => 140.00
        ]);
    }
}
