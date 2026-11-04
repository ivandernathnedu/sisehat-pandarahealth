<?php
$files = glob(__DIR__ . '/app/Models/*.php');
foreach($files as $file) {
    $content = file_get_contents($file);
    // Replace fillable array
    $content = preg_replace('/protected \$fillable = \[.*?\];/s', 'protected $guarded = [];', $content);
    // Remove timestamps false if we now have timestamps in ERD
    if (basename($file) !== 'User.php' && basename($file) !== 'AssessmentToken.php') {
        $content = preg_replace('/public \$timestamps = false;/s', '', $content);
        $content = preg_replace('/public const UPDATED_AT = null;/s', '', $content);
    }
    
    // Check for primary keys
    if (basename($file) == 'Factor.php') {
        $content = preg_replace('/protected \$primaryKey = \'factor_id\';/s', 'protected $primaryKey = \'factor_id\';', $content);
    }
    if (basename($file) == 'Question.php') {
        $content = preg_replace('/protected \$primaryKey = \'question_id\';/s', 'protected $primaryKey = \'question_id\';', $content);
    }
    
    file_put_contents($file, $content);
}
echo "Models updated.";
