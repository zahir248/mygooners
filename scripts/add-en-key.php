<?php
$path = dirname(__DIR__) . '/resources/lang/en.json';
$en = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
$en['Pilih kaedah pembayaran untuk pesanan #:number'] = 'Select a payment method for order #:number';
if (! isset($en['Ingat kata laluan anda?'])) {
    $en['Ingat kata laluan anda?'] = 'Remember your password?';
}
ksort($en);
file_put_contents($path, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "done\n";
