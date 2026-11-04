<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('factor_scores')) {
            Schema::create('factor_scores', function (Blueprint $blueprint) {
                $blueprint->increments('factor_score_id');
                $blueprint->unsignedInteger('health_score_id')->nullable();
                $blueprint->unsignedInteger('factor_id')->nullable();
                $blueprint->decimal('score', 8, 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('factor_scores');
    }
};
