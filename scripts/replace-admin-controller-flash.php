<?php

/**
 * Replace hardcoded Malay flash messages in Admin controllers with __('flash.key').
 * Run: php scripts/replace-admin-controller-flash.php
 */

$base = dirname(__DIR__);
$ms = require $base . '/resources/lang/ms/flash.php';

$controllersPath = $base . '/app/Http/Controllers/Admin';
$files = glob($controllersPath . '/*.php');

$reverse = [];
foreach ($ms as $key => $malay) {
    $reverse[$malay] = $key;
}

$count = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;

    foreach ($reverse as $malay => $key) {
        $escaped = preg_quote($malay, '/');
        $content = preg_replace(
            "/->with\\('success',\\s*'{$escaped}'\\)/",
            "->with('success', __('flash.{$key}'))",
            $content
        );
        $content = preg_replace(
            "/->with\\('error',\\s*'{$escaped}'\\)/",
            "->with('error', __('flash.{$key}'))",
            $content
        );
        $content = preg_replace(
            "/->with\\('success',\\s*\"{$escaped}\"\\)/",
            "->with('success', __('flash.{$key}'))",
            $content
        );
        $content = preg_replace(
            "/->with\\('error',\\s*\"{$escaped}\"\\)/",
            "->with('error', __('flash.{$key}'))",
            $content
        );
    }

    // Log file not found (English in codebase)
    $content = str_replace(
        "->with('error', 'Log file not found.')",
        "->with('error', __('flash.log_file_not_found'))",
        $content
    );

    if ($content !== $original) {
        file_put_contents($file, $content);
        $count++;
        echo 'Updated: ' . basename($file) . "\n";
    }
}

echo "Done. {$count} controller files updated.\n";
