<?php

$base = dirname(__DIR__);
$ms = require $base . '/resources/lang/ms/client_messages.php';
$en = require $base . '/resources/lang/en/client_messages.php';
$used = [];

foreach (glob($base . '/app/Http/Controllers/Client/*.php') as $f) {
    $c = file_get_contents($f);
    if (preg_match_all("/__\('client_messages\.([^']+)'/", $c, $m)) {
        foreach ($m[1] as $k) {
            $used[$k] = true;
        }
    }
}

foreach (array_keys($used) as $k) {
    if (! isset($ms[$k])) {
        echo "Missing MS: {$k}\n";
    }
    if (! isset($en[$k])) {
        echo "Missing EN: {$k}\n";
    }
}

echo 'Used keys: ' . count($used) . "\n";
