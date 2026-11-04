<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom max_score
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'max_score')) {
                $table->integer('max_score')->default(5)->after('urutan');
            }
        });

        // 2. Set Q1 - Q6 ke skala 3
        DB::table('questions')
            ->whereBetween('question_id', [1, 6])
            ->update(['max_score' => 3]);
            
        // 3. (Opsional) Jika ada pertanyaan lain yang spesifik 3 poin bisa ditambahkan di sini
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('max_score');
        });
    }
};
