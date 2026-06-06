<?php

$base = dirname(__DIR__);
$en = json_decode(file_get_contents($base . '/resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);
$paths = [$base . '/resources/views/client', $base . '/resources/views/layouts/app.blade.php'];
$updated = 0;

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

        $content = file_get_contents($path);
        $orig = $content;

        $content = preg_replace_callback(
            '/<strong>([^<]+)<\/strong>/u',
            function ($m) use ($en) {
                $text = trim($m[1]);
                if (! isset($en[$text]) || str_contains($m[0], '__(')) {
                    return $m[0];
                }
                $q = str_replace("'", "\\'", $text);

                return "<strong>{{ __('{$q}') }}</strong>";
            },
            $content
        );

        foreach (array_keys($en) as $key) {
            if (strlen($key) < 2 || strlen($key) > 40 || str_contains($key, "\n")) {
                continue;
            }
            $q = str_replace("'", "\\'", $key);
            $wrap = "{{ __('{$q}') }}";
            $escaped = preg_quote($key, '/');
            $content = preg_replace(
                '/(class="[^"]*")>' . $escaped . '<\/a>/u',
                '$1>' . $wrap . '</a>',
                $content
            );
            $content = preg_replace(
                '/(class="[^"]*")>' . $escaped . '<\/div>/u',
                '$1>' . $wrap . '</div>',
                $content
            );
        }

        if ($content !== $orig) {
            file_put_contents($path, $content);
            $updated++;
        }
    }
}

echo "Updated {$updated} files\n";
