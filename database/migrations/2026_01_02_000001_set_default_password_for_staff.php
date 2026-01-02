<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Set default password 'staff1230' for all users with role='staff'
     *
     * @return void
     */
    public function up()
    {
        // Hash the default password
        $defaultPassword = Hash::make('staff1230');
        
        // Update all users with role='staff' to have the default password
        DB::table('users')
            ->where('role', 'staff')
            ->update([
                'password' => $defaultPassword,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        // Log the number of affected rows
        $affectedRows = DB::table('users')
            ->where('role', 'staff')
            ->count();
            
        echo "Updated {$affectedRows} staff user(s) with default password.\n";
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
