<?php

/**
 * Undo incorrect __() wraps on HTML attributes and PHP property/method access.
 */
$base = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base . '/resources/views')
);

$replacements = [
    "\$product->{{ __('calculated_stock') }}" => '$product->calculated_stock',
    "\$product->{{ __('stock_quantity') }}" => '$product->stock_quantity',
    "\$variation->{{ __('stock_quantity') }}" => '$variation->stock_quantity',
    "\$order->created_at->{{ __('diffInHours(now())') }}" => '$order->created_at->diffInHours(now())',
    "{{ __('style=\"display:block\"') }}" => 'style="display:block"',
    "{{ __('data-ad-client=\"ca-pub-1340046473498925\"') }}" => 'data-ad-client="ca-pub-1340046473498925"',
    "{{ __('data-ad-slot=\"7291074183\"') }}" => 'data-ad-slot="7291074183"',
    "{{ __('data-ad-format=\"auto\"') }}" => 'data-ad-format="auto"',
    "{{ __('style=\"background-color: #25D366; color: #fff;\"') }}" => 'style="background-color: #25D366; color: #fff;"',
];

/** Unwrap mistaken __() on HTML/Alpine attribute fragments. */
function unwrapAttributeI18n(string $content): string
{
    return preg_replace(
        '/\{\{\s*__\(\'((?:name|type|autocomplete)="[^"]*"|required|showTermsModal:[^\'"]+|showPrivacyModal[^\'"]*)\'\)\s*\}\}/',
        '$1',
        $content
    ) ?? $content;
}

$updated = 0;
foreach ($iterator as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }
    $path = $file->getPathname();
    $content = file_get_contents($path);
    $orig = $content;
    foreach ($replacements as $from => $to) {
        $content = str_replace($from, $to, $content);
    }
    $content = unwrapAttributeI18n($content);
    if ($content !== $orig) {
        file_put_contents($path, $content);
        $updated++;
        echo str_replace($base . DIRECTORY_SEPARATOR, '', $path) . "\n";
    }
}

echo "Fixed {$updated} files\n";
