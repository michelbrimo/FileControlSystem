<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'username' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            // Add any other required fields for the users table
        ]);
    }
}
