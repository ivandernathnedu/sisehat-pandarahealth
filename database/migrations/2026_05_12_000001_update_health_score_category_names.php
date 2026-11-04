<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update data lama di tabel health_scores
        DB::table('health_scores')->where('category', 'Healthy')->update(['category' => 'SANGAT_SEHAT']);
        DB::table('health_scores')->where('category', 'Moderate')->update(['category' => 'SEHAT']);
        DB::table('health_scores')->where('category', 'Weak')->update(['category' => 'CUKUP_SEHAT']);
        DB::table('health_scores')->where('category', 'Critical')->update(['category' => 'KURANG_SEHAT']);

        echo "Database health_scores updated to Indonesian categories.\n";
    }

    public function down(): void
    {
        // Kembalikan jika perlu (Rollback)
        DB::table('health_scores')->where('category', 'SANGAT_SEHAT')->update(['category' => 'Healthy']);
        DB::table('health_scores')->where('category', 'SEHAT')->update(['category' => 'Moderate']);
        DB::table('health_scores')->where('category', 'CUKUP_SEHAT')->update(['category' => 'Weak']);
        DB::table('health_scores')->where('category', 'KURANG_SEHAT')->update(['category' => 'Critical']);
    }
};
