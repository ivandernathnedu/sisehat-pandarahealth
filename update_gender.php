<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Owner;

try {
    echo "Modifying column to VARCHAR...\n";
    DB::statement("ALTER TABLE owners MODIFY COLUMN gender VARCHAR(20)");
    
    echo "Updating 'male' to 'laki-laki'...\n";
    $m = Owner::where('gender', 'male')->update(['gender' => 'laki-laki']);
    echo "Updated $m records.\n";
    
    echo "Updating 'female' to 'perempuan'...\n";
    $f = Owner::where('gender', 'female')->update(['gender' => 'perempuan']);
    echo "Updated $f records.\n";
    
    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
