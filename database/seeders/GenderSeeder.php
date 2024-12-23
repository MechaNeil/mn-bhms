<?php

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid duplicates
        if (Gender::count() > 0) {
            return;
        }
        
        Gender::insert([
            [
                'name' => 'Male'
            ],
            [
                'name' => 'Female'
            ],
            [
                'name' => 'Cant be determined'
            ],
            [
                'name' => 'Rather not say'
            ],
        ]);
    }
}
