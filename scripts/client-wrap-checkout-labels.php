<?php

/**
 * Wrap remaining checkout/order status labels with __().
 */
$base = dirname(__DIR__);
$labels = [
    'Sedang Diproses',
    'Dibatalkan',
    'Dikembalikan',
    'Gagal',
    'Cuba Bayar Semula',
    'Menunggu Pembayaran',
    'Menunggu Semakan',
    'Telah Dihantar',
    'Telah Diterima (Auto)',
    'Telah Diterima',
    'Telah Dibayar',
    'Pesanan Menunggu Pembayaran',
    'Pesanan Sedang Diproses',
    'Pesanan Telah Dihantar',
    'Pesanan Telah Diterima',
    'Pesanan Dibatalkan/Dikembalikan',
    'Tiada Pesanan Menunggu Pembayaran',
    'Tiada Pesanan Sedang Diproses',
    'Tiada Pesanan Telah Dihantar',
    'Tiada Pesanan Telah Diterima',
    'Tiada Pesanan Dibatalkan/Dikembalikan',
    'Tiada Pesanan Dikembalikan',
    'Tiada pesanan dengan status "Menunggu Pembayaran" ditemui.',
    'Tiada pesanan dengan status "Sedang Diproses" ditemui.',
    'Tiada pesanan dengan status "Telah Dihantar" ditemui.',
    'Tiada pesanan dengan status "Telah Diterima" ditemui.',
    'Tiada pesanan dengan status "Dibatalkan/Dikembalikan" ditemui.',
    'Tiada pesanan dengan status "Dikembalikan" ditemui.',
    'Bayar Pesanan',
    'Bayar Sekarang',
];

$dirs = [
    $base . '/resources/views/client/checkout',
    $base . '/resources/views/client/direct-checkout',
];

$updated = 0;
foreach ($dirs as $dir) {
    if (! is_dir($dir)) {
        continue;
    }
    foreach (glob($dir . '/*.blade.php') as $path) {
        $content = file_get_contents($path);
        $orig = $content;
        foreach ($labels as $label) {
            $q = str_replace("'", "\\'", $label);
            $wrap = "{{ __('{$q}') }}";
            $escaped = preg_quote($label, '/');
            $content = preg_replace_callback(
                '/^(\s+)' . $escaped . '\s*$/mu',
                function ($m) use ($wrap) {
                    if (str_contains($m[0], "__(")) {
                        return $m[0];
                    }

                    return $m[1] . $wrap;
                },
                $content
            );
            $content = preg_replace(
                '/\n(\s+)' . $escaped . '\n/u',
                "\n$1{$wrap}\n",
                $content
            );
            $content = preg_replace(
                '/>(\s*)' . $escaped . '(\s*)</u',
                ">{$wrap}<",
                $content
            );
        }
        if ($content !== $orig) {
            file_put_contents($path, $content);
            $updated++;
            echo basename($path) . "\n";
        }
    }
}

echo "Updated {$updated} files\n";
