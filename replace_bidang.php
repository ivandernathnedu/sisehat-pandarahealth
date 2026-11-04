<?php
$file = 'C:/laragon/www/sisehat/app/Http/Controllers/DashboardController.php';
$content = file_get_contents($file);
$content = str_replace('bidang_usaha', 'sektor_usaha', $content);
file_put_contents($file, $content);

$file2 = 'C:/laragon/www/sisehat/resources/views/pages/dashboard.blade.php';
if(file_exists($file2)){
    $content2 = file_get_contents($file2);
    $content2 = str_replace('bidang_usaha', 'sektor_usaha', $content2);
    file_put_contents($file2, $content2);
}
echo "Done.";
