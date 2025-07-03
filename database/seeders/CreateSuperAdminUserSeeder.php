<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateSuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update the admin user
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                // 'password' => bcrypt('password'),
                'phone_number' => '+919878986757'
            ]
        );

        // Ensure the 'super admin' role exists for the 'web' guard
        $role = Role::firstOrCreate(
            ['name' => 'super admin', 'guard_name' => 'api']
        );

        // Get all permissions for the 'web' guard
        $permissions = Permission::where('guard_name', 'api')->pluck('name')->toArray();

        // Sync all permissions to the role
        $role->syncPermissions($permissions);

        // Assign role to the user
        $user->assignRole($role);
    }
}
