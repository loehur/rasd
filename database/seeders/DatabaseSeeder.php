<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create default admin user
        \App\Models\User::create([
            'name' => 'administrator',
            'phone_number' => '81268098300',
            'password' => 'Admin123!', // Will be auto-hashed by User model mutator
            'role' => 'admin',
        ]);
    }
}
