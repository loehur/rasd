<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates test staff users with default password 'staff1230'
     *
     * @return void
     */
    public function run()
    {
        $defaultPassword = Hash::make('staff1230');
        
        $staffUsers = [
            [
                'name' => 'Staff Test 1',
                'phone_number' => '08123456781',
                'password' => $defaultPassword,
                'role' => 'staff',
            ],
            [
                'name' => 'Staff Test 2',
                'phone_number' => '08123456782',
                'password' => $defaultPassword,
                'role' => 'staff',
            ],
            [
                'name' => 'Staff Test 3',
                'phone_number' => '08123456783',
                'password' => $defaultPassword,
                'role' => 'staff',
            ],
            [
                'name' => 'Budi Santoso',
                'phone_number' => '08567890123',
                'password' => $defaultPassword,
                'role' => 'staff',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'phone_number' => '08567890124',
                'password' => $defaultPassword,
                'role' => 'staff',
            ],
        ];

        foreach ($staffUsers as $user) {
            // Check if user already exists
            $existing = DB::table('users')
                ->where('phone_number', $user['phone_number'])
                ->first();
            
            if ($existing) {
                // Update existing user
                DB::table('users')
                    ->where('phone_number', $user['phone_number'])
                    ->update([
                        'name' => $user['name'],
                        'password' => $user['password'],
                        'role' => $user['role'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                echo "Updated: {$user['name']} ({$user['phone_number']})\n";
            } else {
                // Insert new user
                DB::table('users')->insert([
                    'name' => $user['name'],
                    'phone_number' => $user['phone_number'],
                    'password' => $user['password'],
                    'role' => $user['role'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                echo "Created: {$user['name']} ({$user['phone_number']})\n";
            }
        }
        
        echo "\nDefault password for all staff: staff1230\n";
        echo "Total staff users: " . count($staffUsers) . "\n";
    }
}
