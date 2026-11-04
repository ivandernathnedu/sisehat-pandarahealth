<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('umkms')) {
            Schema::create('umkms', function (Blueprint $table) {
                $table->increments('umkm_id');
                $table->unsignedInteger('owner_id');
                $table->string('nama_umkm');
                $table->string('sektor_usaha')->nullable();
                $table->string('umur_usaha')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
