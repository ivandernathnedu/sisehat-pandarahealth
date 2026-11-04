<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$factors = \Illuminate\Support\Facades\DB::table('factors')->get();
file_put_contents('factors_output.json', json_encode($factors, JSON_PRETTY_PRINT));
