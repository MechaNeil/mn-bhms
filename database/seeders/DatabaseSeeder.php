<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bed;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Status;

use App\Models\Assistant;

use App\Models\Permission;
use App\Models\Company;
use App\Models\Property;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create the Admin role
        // ...




        // Seed Properties


        // Seed Genders, Roles, and Statuses
        $this->call([
            GenderSeeder::class,
            StatusSeeder::class,
            RolesSeeder::class,
            AdminSeeder::class,
            OwnerSeeder::class,
            ConstantUtilityBillSeeder::class,
            PaymentMethodSeeder::class, // Added
        ]);

        // Seed Users
        User::factory()->count(10)->create();

        // Seed Companies
        Company::factory()->count(4)->create();

        // Seed Tenants        
        Tenant::factory()->count(20)->create();

        // Seed Properties
        Property::factory()->count(10)->create();

        // Seed Assistant
        Assistant::factory()->count(10)->create();

        // Seed Rooms
        Room::factory()->count(20)->create();

        // Seed Beds
        Bed::factory()->count(10)->create();



        $this->call([

            UtilityTypeSeeder::class,
            UtilityBillSeeder::class,
            BedAssignmentSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            PaymentSeeder::class, // Added
        ]);
    }
}
