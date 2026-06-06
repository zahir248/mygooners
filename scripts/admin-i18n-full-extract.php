<?php

$viewsPath = dirname(__DIR__) . '/resources/views/admin';
$skip = ['language-switcher'];
$found = [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
foreach ($iterator as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }
    $path = str_replace('\\', '/', $file->getPathname());
    foreach ($skip as $s) {
        if (str_contains($path, $s)) {
            continue 2;
        }
    }

    $content = file_get_contents($path);
    if (preg_match_all("/@section\\('title',\\s*'([^'{}]+)'/", $content, $m)) {
        foreach ($m[1] as $s) {
            collectString($s, $found);
        }
    }
    if (preg_match_all('/placeholder="([^"]{3,120})"/u', $content, $m)) {
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
    if (preg_match_all('/textContent\s*=\s*`([^`]{8,300})`/u', $content, $m)) {
        foreach ($m[1] as $s) {
            collectString($s, $found);
        }
    }
}

function collectString(string $s, array &$found): void
{
    $s = trim($s);
    if (strlen($s) < 2 || strlen($s) > 200) {
        return;
    }
    if (preg_match('/^(class|svg|path|http|@|{{|&|\$|#)/', $s)) {
        return;
    }
    if (preg_match('/^[\d\s\.\+\-%:;,\(\)RM\$]+$/u', $s)) {
        return;
    }
    if (str_contains($s, '__(') || str_contains($s, '{{') || str_contains($s, '}}')) {
        return;
    }
    if (preg_match('/^[a-z_]+$/', $s)) {
        return;
    }
    $found[$s] = true;
}

$keys = array_keys($found);
sort($keys);
file_put_contents(dirname(__DIR__) . '/storage/app/admin-strings-all.txt', implode("\n", $keys));
echo count($keys) . " strings written to storage/app/admin-strings-all.txt\n";
