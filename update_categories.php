<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::table('health_scores')->where('category', 'Healthy')->update(['category' => 'SANGAT_SEHAT']);
    DB::table('health_scores')->where('category', 'Moderate')->update(['category' => 'SEHAT']);
    DB::table('health_scores')->where('category', 'Weak')->update(['category' => 'CUKUP_SEHAT']);
    DB::table('health_scores')->where('category', 'Critical')->update(['category' => 'KURANG_SEHAT']);
    
    echo "Update health_scores categories: SUCCESS\n";
} catch (\Exception $e) {
    echo "Update failed: " . $e->getMessage() . "\n";
}
