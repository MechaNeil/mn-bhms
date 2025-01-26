<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'Manage User', 'description' => 'Manage User Account'],
            ['name' => 'View Reports', 'description' => 'View Payment Reports'],
            ['name' => 'Edit Tenants', 'description' => 'Edit Tenant Details'],
            ['name' => 'Edit Company', 'description' => 'Edit Company Details'],
            ['name' => 'Edit Property', 'description' => 'Edit Property Details'],
            ['name' => 'Edit Room', 'description' => 'Edit Room Details'],
            ['name' => 'Edit Bed', 'description' => 'Edit Bed Details'],
            ['name' => 'Edit Payment', 'description' => 'Edit Payment Details'],
            ['name' => 'Edit Payment Type', 'description' => 'Edit Payment Type Details'],
            ['name' => 'Edit Payment Method', 'description' => 'Edit Payment Method Details'],
            // Add more permissions as needed
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
