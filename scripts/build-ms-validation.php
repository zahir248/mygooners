<?php

$base = dirname(__DIR__);
$jsonPath = $base . '/storage/app/ms-php.json';
$outPath = $base . '/resources/lang/ms/validation.php';

$json = json_decode(file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);

$skip = ['failed', 'password', 'throttle', 'reset', 'sent', 'token', 'user', 'verified'];

$lines = [];
foreach ($json as $key => $value) {
    if (in_array($key, $skip, true)) {
        continue;
    }

    $value = str_replace([':Attribute', ':Other', ':Values'], [':attribute', ':other', ':values'], $value);

    if (str_contains($key, '.')) {
        [$rule, $sub] = explode('.', $key, 2);
        if (! isset($lines[$rule]) || ! is_array($lines[$rule])) {
            $lines[$rule] = is_array($lines[$rule] ?? null) ? $lines[$rule] : [];
        }
        if (is_string($lines[$rule] ?? null)) {
            continue;
        }
        $lines[$rule][$sub] = $value;
    } else {
        $lines[$key] = $value;
    }
}

$lines['custom'] = [];
$lines['attributes'] = [
    'name' => 'nama',
    'email' => 'alamat emel',
    'password' => 'kata laluan',
    'password_confirmation' => 'pengesahan kata laluan',
    'current_password' => 'kata laluan semasa',
    'product_id' => 'produk',
    'variation_id' => 'varian',
    'quantity' => 'kuantiti',
    'title' => 'tajuk',
    'description' => 'penerangan',
    'location' => 'lokasi',
    'pricing' => 'harga',
    'contact_info' => 'maklumat hubungan',
    'category' => 'kategori',
    'tags' => 'tag',
    'rating' => 'penilaian',
    'comment' => 'komen',
    'refund_reason' => 'sebab refund',
    'phone' => 'telefon',
    'address' => 'alamat',
    'city' => 'bandar',
    'state' => 'negeri',
    'postal_code' => 'poskod',
    'country' => 'negara',
    'payment_method' => 'kaedah pembayaran',
    'shipping_name' => 'nama penghantaran',
    'shipping_email' => 'emel penghantaran',
    'shipping_phone' => 'telefon penghantaran',
    'shipping_address' => 'alamat penghantaran',
    'shipping_city' => 'bandar penghantaran',
    'shipping_state' => 'negeri penghantaran',
    'shipping_postal_code' => 'poskod penghantaran',
    'shipping_country' => 'negara penghantaran',
    'bank_name' => 'nama bank',
    'bank_account_number' => 'nombor akaun bank',
    'bank_account_holder' => 'pemilik akaun bank',
    'tracking_number' => 'nombor penjejakan',
    'shipping_courier' => 'kurier penghantaran',
];

$export = var_export($lines, true);
$php = "<?php\n\nreturn {$export};\n";
file_put_contents($outPath, $php);
echo "Wrote {$outPath}\n";
