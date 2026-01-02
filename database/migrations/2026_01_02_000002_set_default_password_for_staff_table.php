<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Set default password 'staff1230' for all staff with role='staff' in staff table
     *
     * @return void
     */
    public function up()
    {
        // Hash the default password
        $defaultPassword = password_hash('staff1230', PASSWORD_BCRYPT);
        
        // Update all staff with role='staff' to have the default password
        // Only update rows where password is NULL or empty
        DB::table('staff')
            ->where('role', 'staff')
            ->where(function($query) {
                $query->whereNull('password')
                      ->orWhere('password', '');
            })
            ->update([
                'password' => $defaultPassword,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        // Count total staff with role='staff'
        $totalStaff = DB::table('staff')
            ->where('role', 'staff')
            ->count();
            
        echo "Set default password for staff users with role='staff'.\n";
        echo "Total staff in database: {$totalStaff}\n";
    }

    /**
     * Reverse the migrations.
     *
     * Note: Cannot reverse password changes as we don't store old passwords
     *
     * @return void
     */
    public function down()
    {
        // Cannot reverse password changes
        echo "Warning: Cannot reverse password changes. Old passwords are not stored.\n";
    }
};
