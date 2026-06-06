<?php

/**
 * Second pass: wrap meta descriptions, partial h1 text, and remaining literals.
 */
$base = dirname(__DIR__);
$map = require $base . '/scripts/client-translation-map.php';

$manual = [
    'Selamat Datang ke' => 'Welcome to',
    'Selamat kembali, :name!' => 'Welcome back, :name!',
    'Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video, pasaran perkhidmatan, dan barangan eksklusif. Sertai ribuan Gooners di seluruh dunia.' => 'The best Arsenal fan community featuring the latest news, videos, services marketplace, and exclusive merchandise. Join thousands of Gooners worldwide.',
    'Iklan' => 'Advertisement',
    'Pesanan #' => 'Order #',
];
$map = array_merge($map, $manual);

$enPath = $base . '/resources/lang/en.json';
$en = json_decode(file_get_contents($enPath), true) ?: [];
$en = array_merge($en, $map);
ksort($en);
file_put_contents($enPath, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

$paths = [$base . '/resources/views/client'];
$metaPattern = "/@section\\('meta_description',\\s*'([^'$][^']*)'\\)/";

$filesUpdated = 0;
foreach ($paths as $viewsPath) {
    $fileList = is_file($viewsPath) ? [$viewsPath] : iterator_to_array(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath)), false);

    foreach ($fileList as $file) {
        if (is_string($file)) {
            $path = $file;
        } else {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            $path = $file->getPathname();
        }

        $content = file_get_contents($path);
        $orig = $content;

        $content = preg_replace_callback($metaPattern, function ($m) {
            $q = str_replace("'", "\\'", $m[1]);
            return "@section('meta_description', __('{$q}'))";
        }, $content);

        $content = str_replace(
            'Selamat Datang ke <span class="text-yellow-300">',
            "{{ __('Selamat Datang ke') }} <span class=\"text-yellow-300\">",
            $content
        );

        $content = preg_replace(
            '/<h1([^>]*)>Selamat kembali, \{\{ auth\(\)->user\(\)->name \}\}\!<\/h1>/',
            "<h1$1>{{ __('Selamat kembali, :name!', ['name' => auth()->user()->name]) }}</h1>",
            $content
        );

        $content = preg_replace(
            '/<div class="text-sm text-gray-500 mb-2">Iklan<\/div>/',
            '<div class="text-sm text-gray-500 mb-2">{{ __(\'Iklan\') }}</div>',
            $content
        );

        foreach (['Sunting Ulasan - ', 'Tulis Ulasan - '] as $prefix) {
            $content = preg_replace(
                "/@section\\('title',\\s*'" . preg_quote($prefix, '/') . "' \\. /",
                "@section('title', __('{$prefix}') . ",
                $content
            );
        }

        $keys = array_keys($map);
        usort($keys, fn ($a, $b) => strlen($b) <=> strlen($a));
        foreach ($keys as $key) {
            if (str_contains($key, '__(') || strlen($key) < 2) {
                continue;
            }
            $escaped = preg_quote($key, '/');
            $q = str_replace("'", "\\'", $key);
            $wrap = "{{ __('{$q}') }}";
            $content = preg_replace('/>(?!\s*\{\{ __\()' . $escaped . '(?!<)\s*</u', ">{$wrap}<", $content);
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
        }

        if ($content !== $orig) {
            file_put_contents($path, $content);
            $filesUpdated++;
        }
    }
}

echo "Pass2 updated {$filesUpdated} files\n";
