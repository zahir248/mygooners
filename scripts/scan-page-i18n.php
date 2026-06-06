<?php

$files = [
    'resources/views/client/home.blade.php',
    'resources/views/client/blog/index.blade.php',
    'resources/views/client/blog/show.blade.php',
    'resources/views/client/videos/index.blade.php',
    'resources/views/client/videos/show.blade.php',
];

$patterns = [
    '/>\s*[A-Za-zÀ-ÿ][^<{]*[a-zA-Zà-ÿ]{4,}[^<{]*\s*</u',
    '/title="[^"]{3,}"/',
    "/alert\('[^']+'\)/",
    '/encodeURIComponent\(\'[^\']+\'\)/',
];

foreach ($files as $file) {
    $path = dirname(__DIR__) . '/' . $file;
    $lines = file($path);
    echo "\n=== {$file} ===\n";
    foreach ($lines as $num => $line) {
        if (preg_match('/__\(|@json\(|trans\(/', $line)) {
            continue;
        }
        if (preg_match('/(ulasan|Carian:|Keputusan|Hasil Carian|Semua Produk|Hanya \{\{|Arsenal Shop|Arsenal Services|Check out|Hi \{\{|Saya berminat|Disusun mengikut:|% OFF|No Stock\'|Tambah\'|target="_blank"\)|__\(\'class)/i', $line)) {
            echo ($num + 1) . ': ' . trim($line) . "\n";
        }
    }
}
