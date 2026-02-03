<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;          
use Spatie\Permission\Models\Permission;    
use Spatie\Permission\PermissionRegistrar;   

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Created Permission and assigned roles with permissions
        app() [PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'create articles']);
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'view articles']);
        Permission::create(['name' => 'delete articles']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo(['edit articles','create articles','view articles']);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo(['view articles']);
        
    }
}
