<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$indexes = \DB::select("SHOW INDEX FROM responses");
foreach ($indexes as $index) {
    echo $index->Key_name . "\n";
}
