<?php

$path = dirname(__DIR__) . '/resources/lang/en.json';
$en = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

$add = [
    'Hanya :count lagi' => 'Only :count left',
    'Sila masukkan alamat emel anda.' => 'Please enter your email address.',
    'OK' => 'OK',
    'Total:' => 'Total:',
];

foreach ($add as $k => $v) {
    if (! isset($en[$k]) || $en[$k] === $k) {
        $en[$k] = $v;
    }
}

ksort($en);
file_put_contents($path, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Synced " . count($add) . " keys\n";
