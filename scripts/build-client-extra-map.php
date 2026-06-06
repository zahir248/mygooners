<?php

$base = dirname(__DIR__);
$jsonPath = $base . '/storage/app/client-translations-extra.json';
if (! is_file($jsonPath)) {
    fwrite(STDERR, "Missing {$jsonPath}\n");
    exit(1);
}

$data = json_decode(file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);
$out = "<?php\n\nreturn " . var_export($data, true) . ";\n";
file_put_contents($base . '/scripts/client-translation-map-extra.php', $out);
echo 'Wrote ' . count($data) . " entries to client-translation-map-extra.php\n";
