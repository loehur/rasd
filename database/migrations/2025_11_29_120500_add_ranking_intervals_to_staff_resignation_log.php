<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('staff_resignation_log') && !Schema::hasColumn('staff_resignation_log', 'ranking_intervals')) {
            Schema::table('staff_resignation_log', function (Blueprint $table) {
                $table->string('ranking_intervals')->nullable()->after('report_day');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('staff_resignation_log') && Schema::hasColumn('staff_resignation_log', 'ranking_intervals')) {
            Schema::table('staff_resignation_log', function (Blueprint $table) {
                $table->dropColumn('ranking_intervals');
            });
        }
    }
};

