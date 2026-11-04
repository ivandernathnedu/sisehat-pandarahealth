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
        if (!Schema::hasTable('health_scores')) {
            Schema::create('health_scores', function (Blueprint $table) {
                $table->increments('health_score_id');
                $table->unsignedInteger('assessment_id')->nullable()->unique();
                $table->decimal('overall_score', 5, 2)->nullable();
                $table->string('category', 20)->nullable();
                $table->timestamp('calculated_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_scores');
    }
};
