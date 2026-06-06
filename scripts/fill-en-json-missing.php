<?php

/**
 * Fill en.json with English values for all __('...') keys used in client views.
 */
$base = dirname(__DIR__);
$enPath = $base . '/resources/lang/en.json';
$en = json_decode(file_get_contents($enPath), true, 512, JSON_THROW_ON_ERROR);
$map = require $base . '/scripts/client-translation-map.php';

$extraPath = $base . '/storage/app/client-translations-extra.json';
if (is_file($extraPath)) {
    $map = array_merge($map, json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR));
}

$keys = [];
$paths = [$base . '/resources/views/client', $base . '/resources/views/layouts/app.blade.php'];
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
        preg_match_all("/__\\('((?:\\\\'|[^'])*)'\\)/", file_get_contents($path), $m);
        foreach ($m[1] as $k) {
            $keys[stripcslashes($k)] = true;
        }
    }
}

$added = 0;
$stillMissing = [];
foreach (array_keys($keys) as $key) {
    if (preg_match('/^(faq_|client\.|admin\.)/', $key)) {
        continue;
    }
    if (isset($en[$key]) && $en[$key] !== $key && ($map[$key] ?? $en[$key]) === $en[$key]) {
        continue;
    }
    $english = $map[$key] ?? null;
    if ($english === null || $english === $key) {
        $stillMissing[] = $key;

        continue;
    }
    $en[$key] = $english;
    $added++;
}

ksort($en);
file_put_contents($enPath, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Added {$added} translations; still missing: " . count($stillMissing) . "\n";
if ($stillMissing !== []) {
    file_put_contents($base . '/storage/app/en-still-missing.txt', implode("\n", $stillMissing) . "\n");
}
