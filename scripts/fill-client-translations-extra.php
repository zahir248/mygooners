<?php

/**
 * Merges existing client-translations-extra.json with manual English for client-strings-missing.txt
 */
$base = dirname(__DIR__);
$missingPath = $base . '/storage/app/client-strings-missing.txt';
$extraPath = $base . '/storage/app/client-translations-extra.json';

$missing = array_values(array_filter(array_map('trim', file($missingPath))));
$existing = json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR);
$manual = require __DIR__ . '/client-missing-english-map.php';

$identity = [
    '= start && i' => '= start && i',
    '8nx2p4' => '8nx2p4',
    '])' => '])',
    'diffInHours(now())' => 'diffInHours(now())',
    "gtag('config', 'G-D7JL8SL7SN');" => "gtag('config', 'G-D7JL8SL7SN');",
    '47650 Subang Jaya, Selangor' => '47650 Subang Jaya, Selangor',
    'B-10-02, Second Floor, Garden Shoppe One City' => 'B-10-02, Second Floor, Garden Shoppe One City',
    'Jalan USJ 25/1A, One City' => 'Jalan USJ 25/1A, One City',
];

$map = array_merge($manual, $identity, $existing);

$stillMissing = [];
foreach ($missing as $key) {
    if (! isset($map[$key])) {
        $stillMissing[] = $key;
    }
}

if ($stillMissing !== []) {
    fwrite(STDERR, count($stillMissing) . " strings still without translation:\n");
    foreach (array_slice($stillMissing, 0, 20) as $s) {
        fwrite(STDERR, "  - {$s}\n");
    }
    exit(1);
}

ksort($map, SORT_STRING);
file_put_contents(
    $extraPath,
    json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"
);

echo 'Total keys: ' . count($map) . PHP_EOL;
echo 'Missing file covered: ' . (count($missing) - count($stillMissing)) . '/' . count($missing) . PHP_EOL;
