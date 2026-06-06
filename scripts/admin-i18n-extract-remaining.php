<?php

/**
 * Find admin blade lines with likely Malay/UI text not wrapped in __().
 * Run: php scripts/admin-i18n-extract-remaining.php
 */

$viewsPath = dirname(__DIR__) . '/resources/views/admin';
$skip = ['language-switcher', '/auth/'];
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

    $lines = file($path);
    foreach ($lines as $num => $line) {
        if (str_contains($line, '__(') || str_contains($line, '@lang(')) {
            continue;
        }
        if (preg_match('/@(php|if|else|endif|foreach|endforeach|switch|case|break|extends|section|push|endpush|stack|csrf|json|forelse|empty|endforelse|include)/', $line)) {
            continue;
        }
        if (preg_match('/>\s*([A-Za-zÀ-ÿ][A-Za-zÀ-ÿ0-9\s\.,!?\-:;\'()\/]+?)\s*</u', $line, $m)) {
            $text = trim($m[1]);
            if (strlen($text) < 4 || strlen($text) > 100) {
                continue;
            }
            if (preg_match('/^(class|id|http|www|svg|path|button|submit|GET|POST|px-|py-|bg-|text-|flex|grid|route)/i', $text)) {
                continue;
            }
            if (preg_match('/^\d|^[A-Z]{2,}$/', $text)) {
                continue;
            }
            $found[$text] = ($found[$text] ?? 0) + 1;
        }
        if (preg_match("/@(section|placeholder)\([^)]*'([^']{4,})'/", $line, $m)) {
            $found[$m[2]] = ($found[$m[2]] ?? 0) + 1;
        }
    }
}

arsort($found);
echo count($found) . " unwrapped strings (sample top 80):\n";
$i = 0;
foreach ($found as $text => $count) {
    echo "- [{$count}] {$text}\n";
    if (++$i >= 80) {
        break;
    }
}
