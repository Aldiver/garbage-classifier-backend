<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 20 student records
        Student::factory(20)->create();

        // Create an admin user with the specified email and password
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('1234567890'),  // Hash the password
        ]);
    }
}
