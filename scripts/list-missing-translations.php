<?php

$base = dirname(__DIR__);
$all = array_filter(array_map('trim', file($base . '/storage/app/admin-strings-all.txt')));
$map = require $base . '/scripts/admin-translation-map.php';
$missing = array_values(array_diff($all, array_keys($map)));
sort($missing);
file_put_contents($base . '/storage/app/admin-strings-missing.txt', implode("\n", $missing));
echo count($missing) . " missing of " . count($all) . " total\n";
