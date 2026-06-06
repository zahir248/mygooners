<?php

$base = dirname(__DIR__);
$strings = array_filter(array_map('trim', file($base . '/storage/app/client-strings-all.txt')));
$enJson = json_decode(file_get_contents($base . '/resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);

$map = [];
$missing = [];
foreach ($strings as $s) {
    if (isset($enJson[$s])) {
        $map[$s] = $enJson[$s];
    } else {
        $missing[] = $s;
    }
}

$extraPath = $base . '/storage/app/client-translations-extra.json';
$existing = is_file($extraPath)
    ? json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR)
    : [];
$map = array_merge($map, $existing);

file_put_contents($base . '/storage/app/client-translations-extra.json', json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
file_put_contents($base . '/storage/app/client-strings-missing.txt', implode("\n", $missing));

echo 'Mapped ' . (count($strings) - count($missing)) . ' from en.json, ' . count($missing) . " still missing\n";
