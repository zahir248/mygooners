<?php

/**
 * One-time helper: build en.json translations and wrap admin blade strings with __().
 * Run: php scripts/admin-i18n-migrate.php
 */

$basePath = dirname(__DIR__);
$viewsPath = $basePath . '/resources/views/admin';
$langPath = $basePath . '/resources/lang';

// Malay => English (extend as needed)
$translations = require $basePath . '/scripts/admin-translation-map.php';

// Write en.json (merge with existing admin.php keys via JSON for string literals)
$enJsonPath = $langPath . '/en.json';
$existing = [];
if (is_file($enJsonPath)) {
    $existing = json_decode(file_get_contents($enJsonPath), true) ?: [];
}
$merged = array_merge($existing, $translations);
ksort($merged);
file_put_contents($enJsonPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

echo 'Wrote ' . count($merged) . " entries to en.json\n";

$skipFiles = ['language-switcher.blade.php'];
$skipDirs = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath)
);

$wrapCount = 0;
$fileCount = 0;

foreach ($iterator as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    foreach ($skipFiles as $skip) {
        if (str_ends_with($path, $skip)) {
            continue 2;
        }
    }
    foreach ($skipDirs as $skipDir) {
        if (str_contains(str_replace('\\', '/', $path), $skipDir)) {
            continue 2;
        }
    }

    $content = file_get_contents($path);
    $original = $content;

    // Longest keys first to avoid partial replacements
    $keys = array_keys($translations);
    usort($keys, fn ($a, $b) => strlen($b) <=> strlen($a));

    foreach ($keys as $key) {
        if (str_contains($key, '__(') || str_contains($key, '{{')) {
            continue;
        }

        $escaped = preg_quote($key, '/');

        // @section('title', '...')
        $content = preg_replace(
            "/@section\\('title',\\s*'{$escaped}'\\)/",
            "@section('title', __('" . str_replace("'", "\\'", $key) . "'))",
            $content
        );

        // >Text< (not already wrapped)
        $content = preg_replace(
            '/>(?!\s*\{\{ __\()' . $escaped . '(?!<)\s*</u',
            ">{{ __('" . str_replace("'", "\\'", $key) . "') }}<",
            $content
        );

        // placeholder="..."
        $content = preg_replace(
            '/placeholder="' . $escaped . '"/u',
            'placeholder="{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}"',
            $content
        );

        // title="..." tooltips (skip if already blade)
        $content = preg_replace(
            '/title="' . $escaped . '"/u',
            'title="{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}"',
            $content
        );

        // aria-label="..."
        $content = preg_replace(
            '/aria-label="' . $escaped . '"/u',
            'aria-label="{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}"',
            $content
        );

        // <option ...>Text</option> (simple)
        $content = preg_replace(
            '/(<option[^>]*>)' . $escaped . '(<\/option>)/u',
            '$1{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}$2',
            $content
        );

        // <label ...>Text</label>
        $content = preg_replace(
            '/(<label[^>]*>)' . $escaped . '(<\/label>)/u',
            '$1{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}$2',
            $content
        );

        // <h1...>Text</h1>, h2, h3, p, span, button, a, th, dt, dd
        foreach (['h1', 'h2', 'h3', 'h4', 'p', 'span', 'button', 'a', 'th', 'dt', 'dd', 'label'] as $tag) {
            $content = preg_replace(
                '/(<' . $tag . '[^>]*>)' . $escaped . '(<\/' . $tag . '>)/u',
                '$1{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}$2',
                $content
            );
            // Multiline: <label>\n    Text\n</label>
            $content = preg_replace(
                '/(<' . $tag . '[^>]*>)\s*\n\s*' . $escaped . '\s*\n\s*(<\/' . $tag . '>)/u',
                '$1{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}$2',
                $content
            );
        }

        // Text on its own line before closing tag (common in buttons with icons)
        $content = preg_replace(
            '/\n(\s+)' . $escaped . '\n(\s*<\/(?:a|button|label|span|h1|h2|h3|h4|p|div|td|th)>)/u',
            "\n$1{{ __('" . str_replace("'", "\\'", $key) . "') }}\n$2",
            $content
        );

        // After </svg> icon blocks
        $content = preg_replace(
            '/(<\/svg>\s*\n\s*)' . $escaped . '(\s*\n)/u',
            "$1{{ __('" . str_replace("'", "\\'", $key) . "') }}$2",
            $content
        );

        // confirm('...')
        $content = preg_replace(
            "/confirm\\('" . $escaped . "'\\)/u",
            "confirm(@json(__('" . str_replace("'", "\\'", $key) . "')))",
            $content
        );

        // textContent = '...'
        $content = preg_replace(
            "/textContent = '" . $escaped . "'/u",
            "textContent = @json(__('" . str_replace("'", "\\'", $key) . "'))",
            $content
        );

        // innerHTML alt="..."
        $content = preg_replace(
            '/\balt="' . $escaped . '"/u',
            'alt="{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}"',
            $content
        );

        // Label text before nested element: >Sahkan Kata Laluan <span
        $content = preg_replace(
            '/>' . $escaped . '\s+(<span)/u',
            ">{{ __('" . str_replace("'", "\\'", $key) . "') }} $1",
            $content
        );

        // th/td standalone header cells
        $content = preg_replace(
            '/(<th[^>]*>)\s*' . $escaped . '\s*(<\/th>)/u',
            '$1{{ __(\'' . str_replace("'", "\\'", $key) . '\') }}$2',
            $content
        );
    }

    // Fix double-wrapping
    $content = preg_replace(
        "/\{\{ __\('([^']+)'\) \}\}\s*\{\{ __\('\\1'\) \}\}/",
        "{{ __('$1') }}",
        $content
    );

    if ($content !== $original) {
        file_put_contents($path, $content);
        $fileCount++;
        $wrapCount += substr_count($content, "__('") - substr_count($original, "__('");
    }
}

echo "Updated {$fileCount} blade files\n";
