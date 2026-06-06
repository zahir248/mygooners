<?php

$base = dirname(__DIR__);
$remaining = array_filter(array_map('trim', file($base . '/storage/app/admin-strings-all.txt')));
$extraPath = $base . '/storage/app/admin-translations-extra.json';
$extra = json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR);

$patch = [
    'Akhiri dengan seruan untuk tindakan' => 'End with a call to action',
    'Aktif' => 'Active',
    'Artikel Diterbitkan' => 'Articles Published',
    'Berikan maklumat tentang kemas kini komuniti' => 'Share community update information',
    'Butiran Pesanan -' => 'Order Details -',
    'Dibatalkan' => 'Cancelled',
    'Dicipta:' => 'Created:',
    'Dikemas kini:' => 'Updated:',
    'Dikembalikan' => 'Returned',
    'Diluluskan' => 'Approved',
    'Diproses' => 'Processed',
    'Diterbitkan' => 'Published',
    'Ditolak' => 'Rejected',
    'Gagal' => 'Failed',
    'Gambar Varian:' => 'Variant Image:',
    'Harga Jualan:' => 'Sale Price:',
    'Harga:' => 'Price:',
    'Huraian Meta:' => 'Meta Description:',
    'Jumlah Jualan' => 'Total Sales',
    'Kawasan Operasi:' => 'Operating Area:',
    'Kemahiran:' => 'Skills:',
    'Kongsi berita Arsenal terkini' => 'Share the latest Arsenal news',
    'Lain-lain' => 'Other',
    'Lihat nilai asal' => 'View original value',
    'Log Masuk Pentadbir - MyGooners' => 'Admin Login - MyGooners',
    'Lupa Kata Laluan Pentadbir - MyGooners' => 'Forgot Admin Password - MyGooners',
    'Menunggu' => 'Pending',
    'Minta Akses Pentadbir - MyGooners' => 'Request Admin Access - MyGooners',
    'Mulakan dengan salam yang mesra' => 'Start with a friendly greeting',
    'Pastikan kandungan adalah tepat sebelum dihantar' => 'Ensure content is accurate before sending',
    'Pengalaman:' => 'Experience:',
    'Perkhidmatan Aktif' => 'Active Services',
    'Pratonton:' => 'Preview:',
    'Proses ini mungkin mengambil masa beberapa minit' => 'This process may take several minutes',
    'Ringkasan:' => 'Summary:',
    'SKU:' => 'SKU:',
    'Sedang Diproses' => 'Processing',
    'Selesai' => 'Completed',
    'Semua Perkhidmatan' => 'All Services',
    'Semua Produk' => 'All Products',
    'Semua Ulasan' => 'All Reviews',
    'Semua entri log akan dipadam secara kekal' => 'All log entries will be permanently deleted',
    'Sertakan pautan ke artikel atau video' => 'Include a link to an article or video',
    'Set Semula Kata Laluan Pentadbir - MyGooners' => 'Reset Admin Password - MyGooners',
    'Simpan sebagai Draf' => 'Save as Draft',
    'Status:' => 'Status:',
    'Stok:' => 'Stock:',
    'Surat berita akan dihantar kepada semua pelanggan aktif' => 'The newsletter will be sent to all active subscribers',
    'Tajuk Meta:' => 'Meta Title:',
    'Tarikh Penghantaran:' => 'Shipping Date:',
    'Telah Dibayar' => 'Paid',
    'Telah Dihantar' => 'Shipped',
    'Telah Diterima' => 'Received',
    'Tertunggak' => 'Outstanding',
    'Tiada sandaran automatik akan dicipta' => 'No automatic backup will be created',
    'Tidak Aktif' => 'Inactive',
    'Tindakan ini tidak boleh dibatalkan' => 'This action cannot be undone',
    'Ulasan 1 Bintang' => '1-Star Reviews',
    'Ulasan 5 Bintang' => '5-Star Reviews',
    'Ulasan Diberikan' => 'Reviews Given',
    'atau' => 'or',
];

// Couriers / brands stay as-is
foreach (['DHL', 'Deliveroo', 'FedEx', 'Foodpanda', 'Gojek', 'GrabExpress', 'GrabFood', 'J&T', 'Lalamove', 'Lazada Express', 'Ninja Van', 'Pos Malaysia', 'Rider', 'Shopee Express', 'Shopee Food', 'TNT', 'Uber Eats'] as $c) {
    $patch[$c] = $c;
}

$patch['dQw4w9WgXcQ'] = 'dQw4w9WgXcQ';
$patch["formatted_content ?: '"] = "formatted_content ?: '";

$merged = array_merge($extra, $patch);
file_put_contents($extraPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo 'Merged ' . count($patch) . ' patch keys; total ' . count($merged) . "\n";
