<?php

/**
 * Replace en.json entries where English value still equals the Malay key.
 */
$base = dirname(__DIR__);
$enPath = $base . '/resources/lang/en.json';
$en = json_decode(file_get_contents($enPath), true, 512, JSON_THROW_ON_ERROR);
$map = array_merge(
    require $base . '/scripts/client-translation-map.php',
    require $base . '/scripts/client-en-ui-overrides.php'
);
$extraPath = $base . '/storage/app/client-translations-extra.json';
if (is_file($extraPath)) {
    $map = array_merge($map, json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR));
}
$overrides = require $base . '/scripts/client-en-ui-overrides.php';
$map = array_merge($map, $overrides);

$fixed = 0;
$still = [];
foreach ($en as $key => $value) {
    if ($value !== $key) {
        continue;
    }
    $english = $map[$key] ?? null;
    if ($english !== null && $english !== $key) {
        $en[$key] = $english;
        $fixed++;

        continue;
    }
    $still[] = $key;
}

ksort($en);
file_put_contents($enPath, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Fixed {$fixed} placeholder entries; still untranslated in en.json: " . count($still) . "\n";
file_put_contents($base . '/storage/app/en-still-missing.txt', implode("\n", $still) . "\n");
