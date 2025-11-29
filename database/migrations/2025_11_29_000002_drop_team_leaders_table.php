<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop team_leaders table since all data has been migrated to staff table
     */
    public function up(): void
    {
        // First, drop foreign key constraint from attendances table if it exists
        Schema::table('attendances', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['team_leader_id']);
        });

        // Now we can safely drop the team_leaders table
        Schema::dropIfExists('team_leaders');
    }

    /**
     * Reverse the migrations.
     *
     * Recreate team_leaders table if rollback is needed
     */
    public function down(): void
    {
        // Recreate team_leaders table
        Schema::create('team_leaders', function (Blueprint $table) {
            $table->string('employee_id')->primary(); // ID TL as primary key
            $table->string('name');
            $table->string('password');
            $table->enum('work_location', ['WFH', 'Onsite'])->default('Onsite');
            $table->string('position')->default('DC TL');
            $table->string('team')->nullable();
            $table->integer('team_quantity')->nullable();
            $table->string('department')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('rank')->nullable(); // KPI, CPI, C0, C1, C2
            $table->date('first_day_tl')->nullable();
            $table->string('warning_letter')->nullable();
            $table->integer('ojk_case')->default(0);
            $table->string('former_tl')->nullable();
            $table->string('area')->nullable(); // Depok, Jakarta, Tangerang
            $table->timestamps();
        });

        // Recreate foreign key constraint on attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('team_leader_id')
                  ->references('employee_id')
                  ->on('team_leaders')
                  ->onDelete('cascade');
        });
    }
};
