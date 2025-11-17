<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class UserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create user management permissions
        $userPermissions = [
            ['name' => 'user.view', 'guard_name' => 'web'],
            ['name' => 'user.create', 'guard_name' => 'web'],
            ['name' => 'user.edit', 'guard_name' => 'web'],
            ['name' => 'user.delete', 'guard_name' => 'web'],
        ];

        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create role management permissions
        $rolePermissions = [
            ['name' => 'role.view', 'guard_name' => 'web'],
            ['name' => 'role.create', 'guard_name' => 'web'],
            ['name' => 'role.edit', 'guard_name' => 'web'],
            ['name' => 'role.delete', 'guard_name' => 'web'],
        ];

        foreach ($rolePermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create directory management permissions
        $directoryPermissions = [
            ['name' => 'directory.view', 'guard_name' => 'web'],
            ['name' => 'directory.create', 'guard_name' => 'web'],
            ['name' => 'directory.edit', 'guard_name' => 'web'],
            ['name' => 'directory.delete', 'guard_name' => 'web'],
        ];

        foreach ($directoryPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create forum management permissions
        $forumPermissions = [
            ['name' => 'forum.view', 'guard_name' => 'web'],
            ['name' => 'forum.create', 'guard_name' => 'web'],
            ['name' => 'forum.edit', 'guard_name' => 'web'],
            ['name' => 'forum.delete', 'guard_name' => 'web'],
            ['name' => 'forum.approve', 'guard_name' => 'web'],
        ];

        foreach ($forumPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Assign all permissions to Super Admin role (if exists)
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($superAdminRole) {
            $allPermissionNames = [
                'user.view',
                'user.create',
                'user.edit',
                'user.delete',
                'role.view',
                'role.create',
                'role.edit',
                'role.delete',
                'directory.view',
                'directory.create',
                'directory.edit',
                'directory.delete',
                'forum.view',
                'forum.create',
                'forum.edit',
                'forum.delete',
                'forum.approve'
            ];
            $permissions = Permission::whereIn('name', $allPermissionNames)->get();
            $superAdminRole->syncPermissions($permissions);

            $this->command->info('All permissions assigned to Super Admin role successfully!');
        } else {
            $this->command->warn('Super Admin role not found. Please assign permissions manually.');
        }

        $this->command->info('User, Role, Directory, and Forum permissions created successfully!');
    }
}
