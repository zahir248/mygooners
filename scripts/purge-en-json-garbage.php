<?php

require_once __DIR__ . '/unwrap-invalid-i18n.php';

$base = dirname(__DIR__);
$enPath = $base . '/resources/lang/en.json';
$en = json_decode(file_get_contents($enPath), true, 512, JSON_THROW_ON_ERROR);

$removed = 0;
$clean = [];
foreach ($en as $key => $value) {
    if (shouldUnwrap($key)) {
        $removed++;

        continue;
    }
    $clean[$key] = $value;
}

ksort($clean);
file_put_contents($enPath, json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Removed {$removed} garbage keys; kept " . count($clean) . "\n";
