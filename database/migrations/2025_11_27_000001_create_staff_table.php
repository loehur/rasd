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
        Schema::create('staff', function (Blueprint $table) {
            $table->string('staff_id')->primary();
            $table->string('name');
            $table->string('phone_number', 20)->unique();
            $table->string('email')->nullable();
            $table->string('position');
            $table->string('department');
            $table->string('superior')->nullable();
            $table->string('group')->nullable();
            $table->string('area')->nullable();
            $table->string('work_location')->nullable(); // WFH/Onsite
            $table->date('hire_date');
            $table->string('rank')->nullable();
            $table->string('device')->nullable();
            $table->string('team_leader_id')->nullable();
            $table->string('warning_letter')->nullable();
            $table->integer('ojk_case')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff');
    }
};
