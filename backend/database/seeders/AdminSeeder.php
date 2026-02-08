<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        // Create a admin user if not exists.
        $admin = User::firstOrCreate(
            ['email' => 'god@gmail.com'],
            [
                'name'=> 'God',
                'password' => Hash::make('IamGod123'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles([$adminRole]);
    }
}
