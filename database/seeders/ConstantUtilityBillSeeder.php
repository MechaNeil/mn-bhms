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
            'number_of_appliances' => 1,
            'cost' => 20.00
        ]);
        //2
        ConstantUtilityBill::create([
            'number_of_appliances' => 2,
            'cost' => 40.00
        ]);


        ConstantUtilityBill::create([
            'number_of_appliances' => 3,
            'cost' => 60.00
        ]);
        //4
        ConstantUtilityBill::create([
            'number_of_appliances' => 4,
            'cost' => 80.00
        ]);
        //5
        ConstantUtilityBill::create([
            'number_of_appliances' => 5,
            'cost' => 100.00
        ]);
    }
}
