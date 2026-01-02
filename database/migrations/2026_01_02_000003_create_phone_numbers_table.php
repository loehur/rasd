<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id'); // Employee ID
            $table->string('employee_name'); // Employee Name
            $table->string('team_leader_name')->nullable(); // Team Leader Name
            $table->string('phone_number'); // Phone Number (the actual data to store)
            $table->text('remarks')->nullable(); // Remarks/Notes
            $table->timestamps();
            
            // Index for faster queries
            $table->index('staff_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_numbers');
    }
};
