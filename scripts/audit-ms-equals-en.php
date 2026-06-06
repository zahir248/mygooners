<?php

$base = dirname(__DIR__);
$ms = require $base . '/resources/lang/ms/client_messages.php';
$en = require $base . '/resources/lang/en/client_messages.php';

$identical = 0;
foreach ($ms as $k => $v) {
    if (isset($en[$k]) && $ms[$k] === $en[$k]) {
        $identical++;
        echo "MS=EN ({$k}): {$v}\n";
    }
}

echo "\nTotal identical MS/EN values: {$identical}\n";
