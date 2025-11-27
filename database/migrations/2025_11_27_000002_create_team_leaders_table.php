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
        Schema::create('team_leaders', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique(); // ID TL
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_leaders');
    }
};
