<?php

$base = dirname(__DIR__);
$files = [
    $base . '/storage/app/client-strings-all.txt',
    $base . '/storage/app/client-unwrapped.txt',
];
$map = require $base . '/scripts/client-translation-map.php';
$en = json_decode(file_get_contents($base . '/resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);

$added = 0;
foreach ($files as $f) {
    if (! is_file($f)) {
        continue;
    }
    foreach (file($f, FILE_IGNORE_NEW_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strlen($line) < 2) {
            continue;
        }
        if (! isset($map[$line])) {
            $map[$line] = $en[$line] ?? $line;
            $added++;
        }
    }
}

$extraPath = $base . '/storage/app/client-translations-extra.json';
$existing = is_file($extraPath) ? json_decode(file_get_contents($extraPath), true) : [];
$merged = array_merge($existing, $map);
file_put_contents($extraPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

require $base . '/scripts/build-client-extra-map.php';

echo "Added {$added} keys to map; total " . count($merged) . "\n";
