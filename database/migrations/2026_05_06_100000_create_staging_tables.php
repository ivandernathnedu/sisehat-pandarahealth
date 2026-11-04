<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staging_factors', function (Blueprint $table) {
            $table->id();
            $table->string('nama_factor')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
        });

        Schema::create('staging_questions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_factor')->nullable();
            $table->text('pertanyaan')->nullable();
            $table->string('question_role', 50)->nullable();
            $table->integer('urutan')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
        });

        Schema::create('staging_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('nama_factor')->nullable();
            $table->string('category_level', 50)->nullable();
            $table->text('recommendation_text')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
        });

        Schema::create('staging_excel_imports', function (Blueprint $table) {
            $table->id();
            $table->string('nama_owner')->nullable();
            $table->string('email_owner')->nullable();
            $table->string('nama_umkm')->nullable();
            $table->string('sektor_usaha')->nullable();
            $table->string('umur_usaha')->nullable();
            
            // Dynamic Q columns
            for ($i = 1; $i <= 35; $i++) {
                $table->integer("q{$i}")->nullable();
            }

            $table->boolean('is_processed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staging_excel_imports');
        Schema::dropIfExists('staging_recommendations');
        Schema::dropIfExists('staging_questions');
        Schema::dropIfExists('staging_factors');
    }
};
