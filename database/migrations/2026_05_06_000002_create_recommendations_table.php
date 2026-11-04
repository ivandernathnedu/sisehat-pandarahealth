<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('recommendations')) {
            Schema::create('recommendations', function (Blueprint $blueprint) {
                $blueprint->increments('recommendation_id');
                $blueprint->unsignedInteger('factor_id')->nullable();
                $blueprint->string('category_level', 50)->nullable();
                $blueprint->text('recommendation_text')->nullable();
                $blueprint->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
