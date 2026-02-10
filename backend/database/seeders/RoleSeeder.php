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

        $permissions = ['create_articles','edit_articles','view_articles','delete_articles','publish_articles','manage_users'];

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
        $editor->syncPermissions(['create_articles','edit_articles','view_articles','delete_articles']);
        $user->syncPermissions(['view_articles']);
        
    }
}
