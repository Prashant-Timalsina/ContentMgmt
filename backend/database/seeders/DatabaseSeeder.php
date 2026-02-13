<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Roles & Permissions
        $this->call([
            \Database\Seeders\RoleSeeder::class,
        ]);

        // Admin user
        $this->call([
            \Database\Seeders\AdminSeeder::class,
        ]);

        // Articles Seeder
        $this->call([
            \Database\Seeders\ArticleTypeSeeder::class,
        ]);
        
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
            'name' => 'Test User',
            'password' => Hash::make('Test@123'),
            'email_verified_at' => now(),
        ]);
    }
}
