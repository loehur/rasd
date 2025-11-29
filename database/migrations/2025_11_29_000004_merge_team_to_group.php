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
     * Merge team field into group field and drop team column
     */
    public function up(): void
    {
        // Step 1: Copy data from team to group where group is null or empty
        DB::statement("UPDATE staff SET `group` = team WHERE ((`group` IS NULL OR `group` = '') AND team IS NOT NULL AND team != '')");

        // Step 2: Drop the team column
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate team column
        Schema::table('staff', function (Blueprint $table) {
            $table->string('team')->nullable()->after('group');
        });
    }
};
