<?php

$base = dirname(__DIR__);
$paths = [
    $base . '/resources/views/client',
    $base . '/resources/views/layouts/app.blade.php',
];
$skip = ['language-switcher'];
$found = [];

foreach ($paths as $viewsPath) {
    if (is_file($viewsPath)) {
        extractFromFile($viewsPath, $skip, $found);
        continue;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
    foreach ($iterator as $file) {
        if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }
        extractFromFile($file->getPathname(), $skip, $found);
    }
}

function extractFromFile(string $path, array $skip, array &$found): void
{
    $pathNorm = str_replace('\\', '/', $path);
    foreach ($skip as $s) {
        if (str_contains($pathNorm, $s)) {
            return;
        }
    }

    $content = file_get_contents($path);
    if (preg_match_all("/@section\\('title',\\s*'([^'{}]+)'/", $content, $m)) {
        foreach ($m[1] as $s) {
            collectString($s, $found);
        }
    }
    if (preg_match_all('/@section\("title",\s*"([^"{}]+)"/', $content, $m)) {
        foreach ($m[1] as $s) {
            collectString($s, $found);
        }
    }
    if (preg_match_all('/placeholder="([^"]{2,120})"/u', $content, $m)) {
        foreach ($m[1] as $s) {
            if (! str_contains($s, '{{')) {
                collectString($s, $found);
            }
        }
    }
    if (preg_match_all('/>([^<>{}\n]+)</u', $content, $m)) {
        foreach ($m[1] as $s) {
            collectString($s, $found);
        }
    }
    if (preg_match_all('/\n\s+([^\n<>{@][^\n<>{@]*?)\s*\n\s*<\//u', $content, $m)) {
        foreach ($m[1] as $s) {
            collectString($s, $found);
        }
    }
    if (preg_match_all("/confirm\\('([^']{8,200})'\\)/u", $content, $m)) {
        foreach ($m[1] as $s) {
            collectString($s, $found);
        }
    }
    if (preg_match_all('/textContent\s*=\s*[\'`]([^\'`]{4,300})[\'`]/u', $content, $m)) {
        foreach ($m[1] as $s) {
            if (! str_contains($s, '${')) {
                collectString($s, $found);
            }
        }
    }
}

function collectString(string $s, array &$found): void
{
    $s = trim($s);
    if (strlen($s) < 2 || strlen($s) > 200) {
        return;
    }
    if (preg_match('/^(class|svg|path|http|@|{{|&|\$|#|RM)/', $s)) {
        return;
    }
    if (preg_match('/^[\d\s\.\+\-%:;,\(\)RM\$]+$/u', $s)) {
        return;
    }
    if (str_contains($s, '__(') || str_contains($s, '{{') || str_contains($s, '}}')) {
        return;
    }
    if (preg_match('/^[a-z_\.]+$/', $s)) {
        return;
    }
    if (preg_match('/^(GET|POST|PUT|DELETE|true|false|null)$/i', $s)) {
        return;
    }
    $found[$s] = true;
}

$keys = array_keys($found);
sort($keys);
$out = $base . '/storage/app/client-strings-all.txt';
file_put_contents($out, implode("\n", $keys));
echo count($keys) . " strings written to storage/app/client-strings-all.txt\n";
