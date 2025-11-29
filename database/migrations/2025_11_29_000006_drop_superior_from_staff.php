<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (Schema::hasColumn('staff', 'superior')) {
                $table->dropColumn('superior');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'superior')) {
                $table->string('superior')->nullable()->after('department');
            }
        });
    }
};
