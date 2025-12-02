<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create(['name' => 'admin']);
        $competitorRole = Role::create(['name' => 'competidor']);
        $judgeRole = Role::create(['name' => 'juez']);

        // Create Permissions
        $permissions = [
            'create events', 'edit events', 'delete events',
            'create teams', 'edit teams', 'delete teams',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        // Assign all permissions to admin
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        // Assign specific permissions to others (optional, can be expanded later)
        $competitorRole->givePermissionTo(['create teams', 'edit teams']);
    }
}
