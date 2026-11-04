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
        if (!Schema::hasTable('factors')) {
            Schema::create('factors', function (Blueprint $table) {
                $table->increments('factor_id');
                $table->string('nama_factor', 100)->unique()->nullable();
                $table->text('deskripsi')->nullable();
                $table->integer('urutan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factors');
    }
};
