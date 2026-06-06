<?php

$base = dirname(__DIR__);
$enPath = $base . '/resources/lang/en.json';
$en = json_decode(file_get_contents($enPath), true, 512, JSON_THROW_ON_ERROR);
$overrides = require $base . '/scripts/client-en-ui-overrides.php';

$updated = 0;
foreach ($overrides as $key => $english) {
    if ($english !== $key && ($en[$key] ?? $key) !== $english) {
        $en[$key] = $english;
        $updated++;
    }
}

ksort($en);
file_put_contents($enPath, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Applied {$updated} overrides\n";
