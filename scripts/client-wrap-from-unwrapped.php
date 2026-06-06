<?php

/**
 * Wrap strings listed in client-unwrapped.txt when they exist in en.json.
 */
$base = dirname(__DIR__);
$unwrapped = file($base . '/storage/app/client-unwrapped.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$en = json_decode(file_get_contents($base . '/resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);

$keys = array_filter($unwrapped, fn ($k) => isset($en[$k]) && strlen($k) >= 2);
usort($keys, fn ($a, $b) => strlen($b) <=> strlen($a));

// Never process layouts/app.blade.php — use scripts/patch-app-layout-final.php only.
$paths = [
    $base . '/resources/views/client',
];
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
            if (str_contains($file->getPathname(), 'language-switcher')) {
                continue;
            }
            $path = $file->getPathname();
        }

        $content = file_get_contents($path);
        $original = $content;

        foreach ($keys as $key) {
            if (str_contains($key, '__(') || str_contains($key, '{{')) {
                continue;
            }
            $q = str_replace("'", "\\'", $key);
            $wrap = "{{ __('{$q}') }}";
            $escaped = preg_quote($key, '/');

            $content = preg_replace_callback(
                '/^(\s+)' . $escaped . '\s*$/mu',
                fn ($m) => str_contains($m[0], '__(') ? $m[0] : $m[1] . $wrap,
                $content
            );

            $content = preg_replace(
                '/>(?!\s*\{\{ __\()' . $escaped . '(?!<)\s*</u',
                ">{$wrap}<",
                $content
            );

            $content = preg_replace(
                '/\n(\s+)' . $escaped . '\n(\s*<\/(?:a|button|label|span|h1|h2|h3|h4|p|div|td|th|li)>)/u',
                "\n$1{$wrap}\n$2",
                $content
            );

            foreach (['title', 'aria-label'] as $attr) {
                $content = preg_replace(
                    '/' . $attr . '="' . $escaped . '"/u',
                    $attr . '="{{ __(\'' . $q . '\') }}"',
                    $content
                );
            }
        }

        if ($content !== $original) {
            file_put_contents($path, $content);
            $fileCount++;
        }
    }
}

echo "Wrapped keys from list: " . count($keys) . "; updated {$fileCount} files\n";
