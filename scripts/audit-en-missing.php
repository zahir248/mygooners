<?php

$base = dirname(__DIR__);
$en = json_decode(file_get_contents($base . '/resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);
$keys = [];

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base . '/resources/views/client')) as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }
    preg_match_all("/__\\('((?:\\\\'|[^'])*)'\\)/", file_get_contents($file->getPathname()), $m);
    foreach ($m[1] as $k) {
        $keys[stripcslashes($k)] = true;
    }
}

preg_match_all("/__\\('((?:\\\\'|[^'])*)'\\)/", file_get_contents($base . '/resources/views/layouts/app.blade.php'), $m);
foreach ($m[1] as $k) {
    $keys[stripcslashes($k)] = true;
}

$missing = [];
foreach (array_keys($keys) as $k) {
    if (preg_match('/^(faq_|client\.|admin\.)/', $k)) {
        continue;
    }
    if (! isset($en[$k]) || $en[$k] === $k) {
        $missing[] = $k;
    }
}

sort($missing);
file_put_contents($base . '/storage/app/en-missing-keys.txt', implode("\n", $missing) . "\n");
echo count($missing) . " keys missing or untranslated (written to storage/app/en-missing-keys.txt)\n";
