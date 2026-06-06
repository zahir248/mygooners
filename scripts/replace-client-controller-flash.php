<?php

/**
 * Replace hardcoded flash messages in Client controllers with __('client_messages.key').
 * Run after: php scripts/generate-client-messages.php
 */

$base = dirname(__DIR__);
$ms = require $base . '/resources/lang/ms/client_messages.php';

$reverse = [];
foreach ($ms as $key => $malay) {
    $reverse[$malay] = $key;
}

$files = glob($base . '/app/Http/Controllers/Client/*.php');
$count = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;

    uksort($reverse, fn ($a, $b) => strlen($b) <=> strlen($a));

    foreach ($reverse as $malay => $key) {
        $escaped = preg_quote($malay, '/');
        foreach (['success', 'error'] as $type) {
            $content = preg_replace(
                "/->with\\('{$type}',\\s*'{$escaped}'\\)/",
                "->with('{$type}', __('client_messages.{$key}'))",
                $content
            );
            $content = preg_replace(
                "/->with\\('{$type}',\\s*\"{$escaped}\"\\)/",
                "->with('{$type}', __('client_messages.{$key}'))",
                $content
            );
        }
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        $count++;
        echo 'Updated: ' . basename($file) . "\n";
    }
}

echo "Done. {$count} controller files updated.\n";
