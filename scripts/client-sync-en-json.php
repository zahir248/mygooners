<?php

/**
 * Ensure every __('...') key used in client views exists in en.json with an English value.
 */
$base = dirname(__DIR__);
$enPath = $base . '/resources/lang/en.json';
$en = json_decode(file_get_contents($enPath), true, 512, JSON_THROW_ON_ERROR);
$map = require $base . '/scripts/client-translation-map.php';
$extraPath = $base . '/storage/app/client-translations-extra.json';
if (is_file($extraPath)) {
    $map = array_merge($map, json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR));
}

$paths = [$base . '/resources/views/client', $base . '/resources/views/layouts/app.blade.php'];
$keys = [];

foreach ($paths as $viewsPath) {
    $files = is_file($viewsPath)
        ? [$viewsPath]
        : new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));

    foreach ($files as $file) {
        if (is_string($file)) {
            $path = $file;
        } else {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            $path = $file->getPathname();
        }
        $content = file_get_contents($path);
        if (preg_match_all("/__\\('((?:\\\\'|[^'])*)'\\)/", $content, $m)) {
            foreach ($m[1] as $k) {
                $keys[stripcslashes($k)] = true;
            }
        }
    }
}

$added = 0;
foreach (array_keys($keys) as $key) {
    if (! isset($en[$key]) || $en[$key] === $key) {
        if (isset($map[$key]) && $map[$key] !== $key) {
            $en[$key] = $map[$key];
            $added++;
        }
    }
}

ksort($en);
file_put_contents($enPath, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo 'Synced ' . $added . ' new/fixed keys; total en.json keys: ' . count($en) . "\n";
