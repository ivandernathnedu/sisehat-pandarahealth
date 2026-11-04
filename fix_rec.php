<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Assessment;
use App\Services\HealthService;

echo "Memulai sinkronisasi rekomendasi...\n";

$assessments = Assessment::all();
$service = app(HealthService::class);

foreach ($assessments as $a) {
    echo "Memproses Assessment ID: {$a->assessment_id}... ";
    // Ini akan menghitung ulang skor dan otomatis menghubungkan ke rekomendasi baru
    $service->calculate($a->assessment_id);
    echo "Selesai!\n";
}

echo "Semua rekomendasi berhasil disinkronkan.\n";
