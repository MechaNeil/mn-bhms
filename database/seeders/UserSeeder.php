<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 tenants with related models (User, Property, Gender, Status)
        User::insert([
            [
                'first_name' => 'Mark Neil',
                'last_name' => 'Teves',
                'middle_name' => 'Torres',
                'username' => 'Mark Neil Teves',
                'email' => 'mn@gmail.com',
                'password' => Hash::make('12345678'),
                'avatar' => '/empty-user.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ],

        ]);
        
    }
}
