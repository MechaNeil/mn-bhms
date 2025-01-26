<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class AdminSeeder extends Seeder
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
                'username' => 'Neil',
                'email' => 'mn@gmail.com',
                //gender
                'gender_id' => 1,
                //contact_no
                'contact_no' => '09123456789',
                //address
                'address' => 'Begonia, Viga, Catanduanes',
                'password' => Hash::make('12345678'),
                'avatar' => '/empty-user.jpg',
                'role_id' => 1,
                'status_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ],

        ]);
        
    }
}
