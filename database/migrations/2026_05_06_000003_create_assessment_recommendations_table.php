<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assessment_recommendations')) {
            Schema::create('assessment_recommendations', function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->integer('assessment_id');
                $blueprint->integer('recommendation_id');
                $blueprint->timestamp('generated_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_recommendations');
    }
};
