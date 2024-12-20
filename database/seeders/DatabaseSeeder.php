<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * When starting seeding new data to the data base you can
     * either delete the items below or run php artisan db:wipe to 
     * wipe the database and php artisan migrate then 
     * php artisan db:seed to seed the data to the database
     */
    public function run(): void
    {
        // Create the Admin role
        Role::factory()->createAdmin();

        // Create the Tenant and Assistant roles
        Role::factory()->count(2)->create();

        Gender::factory()->count(3)->create();

        // Seed Statuses
        Status::factory()->count(5)->create();

        // Seed Roles

        // Seed Permissions
        Permission::factory()->count(10)->create();

        // Seed Companies
        Company::factory()->count(5)->create();

        // Seed Users
        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) {
                // Assign random role or other relationships if needed
                $user->activityLogs()->createMany(
                    \App\Models\ActivityLog::factory()->count(3)->make()->toArray()
                );
            });
    }
}
