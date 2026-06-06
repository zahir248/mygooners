<?php

$base = dirname(__DIR__);
$paths = [$base . '/resources/views/client', $base . '/resources/views/layouts/app.blade.php'];
$replacements = [
    "?: 'Berita'" => "?: __('Berita')",
    "?: 'Perkhidmatan'" => "?: __('Perkhidmatan')",
    "?: 'Produk'" => "?: __('Produk')",
    "?: 'Harga Rundingan'" => "?: __('Harga Rundingan')",
    "?: 'Lokasi Tidak Dinyatakan'" => "?: __('Lokasi Tidak Dinyatakan')",
    "?: 'Pilihan Varian'" => "?: __('Pilihan Varian')",
    "?: 'varian'" => "?: __('varian')",
    "?: 'Tiada bio dinyatakan'" => "?: __('Tiada bio dinyatakan')",
    "?: 'Tidak dinyatakan'" => "?: __('Tidak dinyatakan')",
    "?: 'Tiada maklumat'" => "?: __('Tiada maklumat')",
    ' tontonan' => " {{ __('tontonan') }}",
];

$count = 0;
foreach ($paths as $viewsPath) {
    $files = is_file($viewsPath)
        ? [$viewsPath]
        : new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));

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
        $new = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($new !== $content) {
            file_put_contents($path, $new);
            $count++;
        }
    }
}

echo "Updated {$count} files with fallback replacements\n";
