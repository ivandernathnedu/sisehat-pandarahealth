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
        Schema::table('umkms', function (Blueprint $table) {
            $table->index('owner_id');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->index('umkm_id');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->index('assessment_id');
            $table->index('question_id');
            $table->index('respondent_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('umkms', function (Blueprint $table) {
            $table->dropIndex(['owner_id']);
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex(['umkm_id']);
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex(['assessment_id']);
            $table->dropIndex(['question_id']);
            $table->dropIndex(['respondent_type']);
        });
    }
};
