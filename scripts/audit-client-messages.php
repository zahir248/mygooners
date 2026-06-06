<?php

$base = dirname(__DIR__);
$ms = require $base . '/resources/lang/ms/client_messages.php';
$en = require $base . '/resources/lang/en/client_messages.php';

$same = 0;
$translated = 0;
foreach ($ms as $k => $v) {
    if (! isset($en[$k])) {
        echo "Missing in EN: {$k}\n";
        continue;
    }
    if ($en[$k] === $v) {
        $same++;
    } else {
        $translated++;
    }
}

echo 'Total keys: ' . count($ms) . "\n";
echo "EN identical to MS (still Malay when locale=en): {$same}\n";
echo "EN properly translated: {$translated}\n";
