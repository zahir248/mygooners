<?php

$base = dirname(__DIR__);
$paths = [$base . '/resources/views/client', $base . '/resources/views/layouts/app.blade.php'];
$found = [];

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
            if (str_contains($file->getPathname(), 'language-switcher')) {
                continue;
            }
            $path = $file->getPathname();
        }

        $lines = file($path);
        foreach ($lines as $num => $line) {
            if (str_contains($line, '__(') || str_contains($line, '@json(__')) {
                continue;
            }
            if (preg_match_all('/>([^<>{}\n]{2,100})</u', $line, $m)) {
                foreach ($m[1] as $s) {
                    $s = trim($s);
                    if (preg_match('/[a-zA-Z]{3,}/', $s) && ! preg_match('/^[\d\s\.\+\-%:;,RM\$@]+$/', $s)) {
                        $found[$s][$path . ':' . ($num + 1)] = true;
                    }
                }
            }
            if (preg_match('/^\s+([A-Za-zÀ-ÿ][^\n<>{]{2,80})\s*$/u', $line, $m)) {
                $s = trim($m[1]);
                if (! preg_match('/^[\$@#\/]/', $s)) {
                    $found[$s][$path . ':' . ($num + 1)] = true;
                }
            }
        }
    }
}

$keys = array_keys($found);
sort($keys);
file_put_contents($base . '/storage/app/client-unwrapped.txt', implode("\n", $keys));
echo count($keys) . " unwrapped strings in storage/app/client-unwrapped.txt\n";
