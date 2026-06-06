<?php

$files = [
    'resources/views/client/cart/index.blade.php',
    'resources/views/client/checkout/index.blade.php',
    'resources/views/client/checkout/show.blade.php',
    'resources/views/client/checkout/orders.blade.php',
    'resources/views/client/checkout/success.blade.php',
    'resources/views/client/checkout/retry-payment.blade.php',
    'resources/views/client/checkout/stripe-payment.blade.php',
    'resources/views/client/dashboard.blade.php',
    'resources/views/client/profile.blade.php',
    'resources/views/client/favourites/index.blade.php',
    'resources/views/client/refunds/index.blade.php',
    'resources/views/client/refunds/create.blade.php',
    'resources/views/client/refunds/show.blade.php',
    'resources/views/client/shipping-details/index.blade.php',
    'resources/views/client/shipping-details/create.blade.php',
    'resources/views/client/shipping-details/edit.blade.php',
    'resources/views/client/billing-details/index.blade.php',
    'resources/views/client/billing-details/create.blade.php',
    'resources/views/client/billing-details/edit.blade.php',
];

$patterns = [
    '/__\(\'(target|class|rel|aria-label)=/',
    '/alert\(\'[^\']+\'\)/',
    '/title="[^"]{4,}"/',
    '/>\s*(Tiada |Semua |Hasil |Keputusan |Hanya |Carian:|No Stock\'|Tambah\'| views|min baca|RM\d)/u',
    '/encodeURIComponent\(\'[^\']+\'\)/',
    '/with\([\'\"]success[\'\"],\s*[\'\"][^_\$]/',
    '/with\([\'\"]error[\'\"],\s*[\'\"][^_\$]/',
];

foreach ($files as $file) {
    $path = dirname(__DIR__) . '/' . $file;
    if (! is_file($path)) {
        echo "MISSING: {$file}\n";
        continue;
    }
    $lines = file($path);
    $hits = [];
    foreach ($lines as $num => $line) {
        if (preg_match('/__\(|@json\(|trans\(/', $line) && ! preg_match($patterns[0], $line)) {
            // still check other patterns
        }
        foreach ($patterns as $p) {
            if (preg_match($p, $line)) {
                $hits[] = ($num + 1) . ': ' . trim($line);
                break;
            }
        }
        // Hardcoded Malay/English without __ on same line
        if (! preg_match('/__\(|@json\(|trans\(|@section|@php|@if|@foreach|@endif|@else|{{ \$|{{--|<!--|\$|route\(|url\(|asset\(|number_format|Str::|auth\(|old\(|session\(/', $line)
            && preg_match('/>([A-Z][a-z]+(\s+[a-z]+){2,})</', $line, $m)) {
            $hits[] = ($num + 1) . ': [text] ' . trim($line);
        }
    }
    if ($hits) {
        echo "\n=== {$file} ===\n";
        foreach (array_slice($hits, 0, 25) as $h) {
            echo $h . "\n";
        }
        if (count($hits) > 25) {
            echo '... and ' . (count($hits) - 25) . " more\n";
        }
    }
}
