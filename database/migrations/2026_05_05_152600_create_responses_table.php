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
        if (!Schema::hasTable('responses')) {
            Schema::create('responses', function (Blueprint $table) {
                $table->increments('response_id');
                $table->unsignedInteger('assessment_id')->nullable();
                $table->unsignedInteger('question_id')->nullable();
                $table->enum('respondent_type', ['owner', 'employee'])->nullable();
                $table->string('employee_code', 50)->nullable();
                $table->integer('answer_value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
