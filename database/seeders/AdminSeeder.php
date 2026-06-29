<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::updateOrCreate(
            ['email' => 'pro@six.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('prosix123'),
            ]
        );

        $admin->assignRole('super-admin');  // ← Yeh line add kar do

        $this->command->info('Super Admin created/updated with super-admin role!');
    }
}
