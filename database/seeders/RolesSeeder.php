<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Carbon\Carbon;


class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid duplicates
        if (Role::count() > 0) {
            return;
        }

        Role::insert([
            [
                'role_name' => 'Tenant',
                'description' => 'Tenant Role'
                ,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), // Optional: if you also want to set

            ],
            [
                'role_name' => 'Assistant',
                'description' => 'Helper for the Owner'
                ,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), // Optional: if you also want to set
            ],
            [
                'role_name' => 'Owner',
                'description' => 'Owner of the Property'
                ,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), // Optional: if you also want to set
            ],
            [
                'role_name' => 'Admin',
                'description' => 'Admin Role'
                ,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), // Optional: if you also want to set
            ],
        ]);
    }
}
