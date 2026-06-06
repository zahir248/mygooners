<?php

/**
 * Builds scripts/admin-translation-map-extra.php from storage/app/admin-translations-extra.json
 * Run: php scripts/build-extra-translation-map.php
 */

$base = dirname(__DIR__);
$jsonPath = $base . '/storage/app/admin-translations-extra.json';
if (! is_file($jsonPath)) {
    fwrite(STDERR, "Missing {$jsonPath}\n");
    exit(1);
}

$data = json_decode(file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);
$out = "<?php\n\nreturn " . var_export($data, true) . ";\n";
file_put_contents($base . '/scripts/admin-translation-map-extra.php', $out);
echo 'Wrote ' . count($data) . " entries to admin-translation-map-extra.php\n";
