<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create staff_logs table to track all changes made to staff records
     */
    public function up(): void
    {
        Schema::create('staff_logs', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id');
            $table->enum('change_type', [
                'division_transfer',
                'promotion',
                'rank_change',
                'warning_letter'
            ]);
            $table->json('old_value')->nullable(); // Store old values as JSON
            $table->json('new_value')->nullable(); // Store new values as JSON
            $table->string('changed_by'); // Admin phone number or ID who made the change
            $table->text('remarks')->nullable(); // Additional notes
            $table->timestamps();

            // Foreign key
            $table->foreign('staff_id')
                  ->references('staff_id')
                  ->on('staff')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_logs');
    }
};
