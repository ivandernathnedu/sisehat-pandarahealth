<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Question;
$ownerQs = Question::where('question_role', 'owner')->get(['question_id', 'pertanyaan', 'max_score'])->toArray();
print_r($ownerQs);
