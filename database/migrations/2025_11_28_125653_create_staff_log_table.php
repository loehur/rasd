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
        Schema::create('staff_log', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id'); // Staff ID yang terkena perubahan
            $table->string('action'); // Action type: resignation, update, etc
            $table->string('field_name')->nullable(); // Field yang diubah
            $table->text('old_value')->nullable(); // Nilai lama
            $table->text('new_value')->nullable(); // Nilai baru
            $table->string('changed_by'); // Team leader phone number
            $table->string('resignation_type')->nullable(); // voluntary/involuntary
            $table->string('resignation_subtype')->nullable(); // personal_reason/management_reason
            $table->date('last_working_day')->nullable(); // Last working day untuk resignation
            $table->date('report_day')->nullable(); // Report day
            $table->timestamps();

            // Foreign key
            $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_log');
    }
};
