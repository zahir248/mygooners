<?php

/**
 * Remove mistaken __('...') wraps on JS/CSS/HTML attributes (not user-visible copy).
 */
$base = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base . '/resources/views/client')
);

function shouldUnwrap(string $inner): bool
{
    if (strlen($inner) < 2) {
        return true;
    }

    $codeSignals = [
        'document.', 'window.', 'classList', 'console.', 'getElementById',
        'querySelector', 'addEventListener', 'function(', '=>', 'const ',
        'let ', 'var ', 'JSON.stringify', 'style.', 'return ', 'if (',
        'elseif', 'endif', '}}', '{{', 'sessionStorage', 'localStorage',
        'textContent', 'innerHTML', 'className', 'preventDefault',
        'method:', 'headers:', 'fetch(', 'stripe.', 'Alpine.',
        'x-transition:', 'x-show:', 'x-data:', 'accept=', 'rows=',
        'onclick=', 'id=', 'name=', 'type=', 'multiple', 'disabled',
        'frameborder', 'font-size:', 'margin:', 'padding:', 'display:',
        'background:', 'border-', 'color:', 'width:', 'height:',
        'overflow', 'position:', 'transform:', 'transition:',
        'float:', 'clear:', 'max-width', 'min-width', 'line-height',
        'vertical-align', 'white-space', 'word-wrap', 'box-shadow',
        'border-radius', 'font-weight', 'font-style', 'scrollbar-',
        'productId:', 'variationId:', 'finalPrice:', 'originalPrice:',
        'selectedVariation', 'productVariations', '@json',
        '8nx2p4', '= start &&', '])', "alert('", 'alert("', 'alert(`',
    ];

    foreach ($codeSignals as $signal) {
        if (str_contains($inner, $signal)) {
            return true;
        }
    }

    if (preg_match('/^[a-z_][\w]*\s*:/', $inner)) {
        return true;
    }

    if (str_ends_with(trim($inner), ';') || str_contains($inner, '();')) {
        return true;
    }

    // Mostly ASCII code / CSS (few Malay letters)
    if (strlen($inner) > 40 && ! preg_match('/[àèìòùáéíóúâêîôûäëïöüñ]/iu', $inner)) {
        $malayHints = ['pesanan', 'anda', 'tiada', 'sila', 'kembali', 'alamat', 'troli', 'beli', 'komuniti', 'perkhidmatan', 'berita', 'ulasan', 'penghantaran', 'pembayaran', 'refund', 'mygooners'];
        $lower = strtolower($inner);
        foreach ($malayHints as $hint) {
            if (str_contains($lower, $hint)) {
                return false;
            }
        }
        if (preg_match('/[{}();=]/', $inner)) {
            return true;
        }
    }

    return false;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') !== realpath(__FILE__)) {
    return;
}

$updated = 0;
$removed = 0;

foreach ($iterator as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }
    $path = $file->getPathname();
    $content = file_get_contents($path);
    $orig = $content;

    $content = preg_replace_callback(
        "/\{\{\s*__\('((?:\\\\'|[^'])*)'\)\s*\}\}/",
        function (array $m) use (&$removed) {
            $inner = stripcslashes($m[1]);
            if (! shouldUnwrap($inner)) {
                return $m[0];
            }
            $removed++;

            return $inner;
        },
        $content
    );

    if ($content !== $orig) {
        file_put_contents($path, $content);
        $updated++;
        echo str_replace($base . DIRECTORY_SEPARATOR, '', $path) . "\n";
    }
}

echo "Unwrapped {$removed} invalid __() in {$updated} files\n";
