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

        //Create Permissions if not exists

        $permissions = ['create articles','edit articles','view articles','delete articles'];

        foreach ($permissions as $permyKun) {
            Permission::firstOrCreate(
                ['name' => $permyKun],
                ['guard_name' => 'api'] 
            );
        }



        // Create role and assign permissions
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'api']  
        );

        $editor = Role::firstOrCreate(
            ['name' => 'editor'],
            ['guard_name' => 'api']
        );

        $user = Role::firstOrCreate(
            ['name' => 'user'],
            ['guard_name' => 'api']
        );
        
        $admin->syncPermissions(Permission::all());
        $editor->syncPermissions(['edit articles','create articles','view articles']);
        $user->syncPermissions(['view articles']);
        
    }
}
