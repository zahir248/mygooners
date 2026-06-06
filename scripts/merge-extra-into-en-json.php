<?php

/**
 * Merge storage/app/client-translations-extra.json into en.json (real English only).
 */
$base = dirname(__DIR__);
$enPath = $base . '/resources/lang/en.json';
$en = json_decode(file_get_contents($enPath), true, 512, JSON_THROW_ON_ERROR);
$extraPath = $base . '/storage/app/client-translations-extra.json';

if (! is_file($extraPath)) {
    fwrite(STDERR, "Missing {$extraPath}\n");
    exit(1);
}

$extra = json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR);
$map = require $base . '/scripts/client-translation-map.php';
$all = array_merge($map, $extra);

$updated = 0;
foreach ($all as $malay => $english) {
    if (! is_string($malay) || ! is_string($english)) {
        continue;
    }
    if ($english === $malay || strlen($malay) < 2) {
        continue;
    }
    if (! isset($en[$malay]) || $en[$malay] === $malay) {
        $en[$malay] = $english;
        $updated++;
    }
}

ksort($en);
file_put_contents($enPath, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Updated {$updated} keys in en.json (total " . count($en) . ")\n";
