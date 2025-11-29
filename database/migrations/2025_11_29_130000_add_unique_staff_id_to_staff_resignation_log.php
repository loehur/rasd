<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('staff_resignation_log')) {
            return;
        }

        // Attempt to remove duplicates (keep latest by created_at)
        try {
            $dups = DB::table('staff_resignation_log')
                ->select('staff_id', DB::raw('COUNT(*) as cnt'))
                ->groupBy('staff_id')
                ->having('cnt', '>', 1)
                ->get();

            foreach ($dups as $dup) {
                $rows = DB::table('staff_resignation_log')
                    ->where('staff_id', $dup->staff_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $keepFirst = true;
                foreach ($rows as $row) {
                    if ($keepFirst) { $keepFirst = false; continue; }
                    DB::table('staff_resignation_log')
                        ->where('staff_id', $dup->staff_id)
                        ->where('created_at', $row->created_at)
                        ->delete();
                }
            }
        } catch (\Throwable $e) {
            // Best-effort; ignore errors in cleanup
        }

        Schema::table('staff_resignation_log', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_resignation_log', 'staff_id')) return;
            $table->unique('staff_id', 'staff_resignation_log_staff_id_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('staff_resignation_log')) {
            return;
        }
        Schema::table('staff_resignation_log', function (Blueprint $table) {
            $table->dropUnique('staff_resignation_log_staff_id_unique');
        });
    }
};

