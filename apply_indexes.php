<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

try {
    Schema::table('umkms', function ($table) {
        $table->index('owner_id');
    });
    echo "umkms index success\n";
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

try {
    Schema::table('assessments', function ($table) {
        $table->index('umkm_id');
    });
    echo "assessments index success\n";
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

try {
    Schema::table('responses', function ($table) {
        $table->index('assessment_id');
        $table->index('question_id');
        $table->index('respondent_type');
    });
    echo "responses index success\n";
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }
