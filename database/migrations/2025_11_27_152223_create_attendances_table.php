<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('work_status'); // WFH/Onsite
            $table->string('staff_id');
            $table->string('name');
            $table->string('position');
            $table->string('superior')->nullable();
            $table->string('department');
            $table->date('hire_date');
            $table->string('rank')->nullable();
            $table->string('device'); // Mobile/PC
            $table->date('report_day');
            $table->date('last_working_day')->nullable();
            $table->string('ranking_intervals')->nullable();
            $table->string('group')->nullable();
            $table->text('reason_for_resign')->nullable();
            $table->string('proof')->nullable(); // File path for proof of attendance
            $table->string('status_code'); // Status & Code (e.g., Leave (C8), Absent (AB4), etc.)
            $table->string('team_leader_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
            $table->foreign('team_leader_id')->references('employee_id')->on('team_leaders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
