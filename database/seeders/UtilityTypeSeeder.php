<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UtilityType;
use Illuminate\Database\Seeder;

class UtilityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UtilityType::create([
            'name' => 'Electricity'
        ]);
        UtilityType::create([
            'name' => 'Water'
        ]);


        
    }
}
