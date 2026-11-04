<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assessment_tokens')) {
            Schema::create('assessment_tokens', function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->integer('assessment_id');
                $blueprint->string('token', 64)->unique();
                $blueprint->timestamp('expired_at');
                $blueprint->boolean('is_active')->default(true);
                $blueprint->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_tokens');
    }
};
