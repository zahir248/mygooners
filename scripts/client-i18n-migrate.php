<?php

/**
 * Build en.json and wrap client blade strings with __().
 * Run: php scripts/client-i18n-migrate.php
 */

$basePath = dirname(__DIR__);
$langPath = $basePath . '/resources/lang';

$translations = require $basePath . '/scripts/client-translation-map.php';

$enJsonPath = $langPath . '/en.json';
$existing = [];
if (is_file($enJsonPath)) {
    $existing = json_decode(file_get_contents($enJsonPath), true) ?: [];
}
$merged = array_merge($existing, $translations);
ksort($merged);
file_put_contents($enJsonPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

echo 'Wrote ' . count($merged) . " entries to en.json\n";

$paths = [
    $basePath . '/resources/views/client',
];
// layouts/app.blade.php: use scripts/apply-app-layout-safe-i18n.php only (CSS/JS must not be wrapped)
$skipFiles = ['language-switcher.blade.php'];

$wrapCount = 0;
$fileCount = 0;

foreach ($paths as $viewsPath) {
    $files = is_file($viewsPath)
        ? [$viewsPath]
        : iterator_to_array(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath)), false);

    foreach ($files as $file) {
        if (is_string($file)) {
            $path = $file;
        } else {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            $path = $file->getPathname();
        }

        foreach ($skipFiles as $skip) {
            if (str_ends_with($path, $skip)) {
                continue 2;
            }
        }

        $content = file_get_contents($path);
        $original = $content;

        $keys = array_keys($translations);
        usort($keys, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($keys as $key) {
            if (str_contains($key, '__(') || str_contains($key, '{{')) {
                continue;
            }

            $escaped = preg_quote($key, '/');
            $q = str_replace("'", "\\'", $key);
            $wrap = "{{ __('{$q}') }}";

            $content = preg_replace(
                "/@section\\('title',\\s*'{$escaped}'\\)/",
                "@section('title', __('{$q}'))",
                $content
            );

            $content = preg_replace(
                '/>(?!\s*\{\{ __\()' . $escaped . '(?!<)\s*</u',
                ">{$wrap}<",
                $content
            );

            $content = preg_replace(
                '/placeholder="' . $escaped . '"/u',
                'placeholder="{{ __(\'' . $q . '\') }}"',
                $content
            );

            $content = preg_replace(
                '/title="' . $escaped . '"/u',
                'title="{{ __(\'' . $q . '\') }}"',
                $content
            );

            $content = preg_replace(
                '/aria-label="' . $escaped . '"/u',
                'aria-label="{{ __(\'' . $q . '\') }}"',
                $content
            );

            $content = preg_replace(
                '/(<option[^>]*>)' . $escaped . '(<\/option>)/u',
                '$1' . $wrap . '$2',
                $content
            );

            foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'button', 'a', 'th', 'td', 'dt', 'dd', 'label', 'li'] as $tag) {
                $content = preg_replace(
                    '/(<' . $tag . '[^>]*>)' . $escaped . '(<\/' . $tag . '>)/u',
                    '$1' . $wrap . '$2',
                    $content
                );
                $content = preg_replace(
                    '/(<' . $tag . '[^>]*>)\s*\n\s*' . $escaped . '\s*\n\s*(<\/' . $tag . '>)/u',
                    '$1' . $wrap . '$2',
                    $content
                );
            }

            $content = preg_replace(
                '/\n(\s+)' . $escaped . '\n(\s*<\/(?:a|button|label|span|h1|h2|h3|h4|p|div|td|th|li)>)/u',
                "\n$1{$wrap}\n$2",
                $content
            );

            $content = preg_replace(
                '/(<\/svg>\s*\n\s*)' . $escaped . '(\s*\n)/u',
                "$1{$wrap}$2",
                $content
            );

            $content = preg_replace(
                "/confirm\\('" . $escaped . "'\\)/u",
                "confirm(@json(__('" . $q . "')))",
                $content
            );

            $content = preg_replace(
                "/textContent = '" . $escaped . "'/u",
                "textContent = @json(__('" . $q . "'))",
                $content
            );

            $content = preg_replace(
                '/\balt="' . $escaped . '"/u',
                'alt="{{ __(\'' . $q . '\') }}"',
                $content
            );

            $content = preg_replace(
                '/(<th[^>]*>)\s*' . $escaped . '\s*(<\/th>)/u',
                '$1' . $wrap . '$2',
                $content
            );
        }

        $content = preg_replace(
            "/\{\{ __\('([^']+)'\) \}\}\s*\{\{ __\('\\1'\) \}\}/",
            "{{ __('$1') }}",
            $content
        );

        if ($content !== $original) {
            file_put_contents($path, $content);
            $fileCount++;
        }
    }
}

echo "Updated {$fileCount} blade files\n";
