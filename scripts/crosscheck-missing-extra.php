<?php

$base = dirname(__DIR__);
$missing = file($base . '/storage/app/en-missing-keys.txt', FILE_IGNORE_NEW_LINES);
$extra = json_decode(file_get_contents($base . '/storage/app/client-translations-extra.json'), true);
$map = require $base . '/scripts/client-translation-map.php';
$all = array_merge($map, $extra);

$fixable = 0;
foreach ($missing as $k) {
    if (isset($all[$k]) && $all[$k] !== $k) {
        $fixable++;
    }
}
echo "Missing from blades: " . count($missing) . ", fixable from map/extra: {$fixable}\n";
