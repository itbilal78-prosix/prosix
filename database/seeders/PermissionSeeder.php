<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions banao (tumhare sidebar ke hisaab se)
        $permissions = [
            'view-dashboard',
            'manage-products',
            'manage-categories',
            'manage-customizer',       // models, patterns, colors, fonts
            'manage-banners',
            'manage-videos',
            'manage-deals',
            'manage-blogs',
            'manage-testimonials',
            'manage-navigation',
            'manage-users',            // customers approve etc
            'manage-website',          // pages, social, etc
            'manage-seo',
            'manage-settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'admin'   // ← guard match
            ]);
        }

        // Super Admin Role
        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'admin'
        ]);

        $superAdmin->syncPermissions(Permission::all());  // sab permissions do

        // Example: Ek limited role (staff)
        $staff = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'admin'
        ]);

        $staff->syncPermissions([
            'view-dashboard',
            'manage-products',
            'manage-categories',
            // sirf jo chaho
        ]);
    }
}