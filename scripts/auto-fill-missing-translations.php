<?php

/**
 * Adds missing admin strings to admin-translations-extra.json using en.json + heuristics.
 * Run after admin-i18n-full-extract.php and list-missing-translations.php
 */

$base = dirname(__DIR__);
$missing = array_filter(array_map('trim', file($base . '/storage/app/admin-strings-missing.txt')));
$extraPath = $base . '/storage/app/admin-translations-extra.json';
$enJsonPath = $base . '/resources/lang/en.json';

$extra = json_decode(file_get_contents($extraPath), true, 512, JSON_THROW_ON_ERROR);
$enJson = json_decode(file_get_contents($enJsonPath), true, 512, JSON_THROW_ON_ERROR);

$wordMap = [
    'Pratonton' => 'Preview',
    'Kembali' => 'Back',
    'Kemas' => 'Update',
    'Kini' => 'Now',
    'Cipta' => 'Create',
    'Simpan' => 'Save',
    'Batal' => 'Cancel',
    'Tapis' => 'Filter',
    'Tambah' => 'Add',
    'Padam' => 'Delete',
    'Luluskan' => 'Approve',
    'Tolak' => 'Reject',
    'Pentadbir' => 'Admin',
    'Utama' => 'Main',
    'Pengguna' => 'User',
    'Perkhidmatan' => 'Service',
    'Produk' => 'Product',
    'Video' => 'Video',
    'Artikel' => 'Article',
    'Pesanan' => 'Order',
    'Aktif' => 'Active',
    'Tidak' => 'Not',
    'Disahkan' => 'Verified',
    'Penulis' => 'Author',
    'Kategori' => 'Category',
    'Tag' => 'Tag',
    'Meta' => 'Meta',
    'Deskripsi' => 'Description',
    'Tajuk' => 'Title',
    'Kata' => 'Word',
    'Kunci' => 'Key',
    'Kata Kunci' => 'Keywords',
    'Tempoh' => 'Duration',
    'Thumbnail' => 'Thumbnail',
    'Buang' => 'Remove',
    'Tandakan' => 'Mark',
    'sebagai' => 'as',
    'pilihan' => 'featured',
    'Langsung' => 'Live',
    'Kosongkan' => 'Clear',
    'Tapisan' => 'Filters',
    'Ulasan' => 'Review',
    'Permohonan' => 'Application',
    'Pengurus' => 'Manager',
    'Penyedia' => 'Provider',
    'Penyemak' => 'Reviewer',
    'Moderator' => 'Moderator',
    'Kandungan' => 'Content',
    'Laporan' => 'Report',
    'Stok' => 'Stock',
    'Jualan' => 'Sales',
    'Bayaran' => 'Payment',
    'Balik' => 'Refund',
    'Penghantaran' => 'Shipping',
    'Penjejakan' => 'Tracking',
    'Nombor' => 'Number',
    'Tarikh' => 'Date',
    'Status' => 'Status',
    'Semua' => 'All',
    'Tiada' => 'None',
    'Muat' => 'Load',
    'turun' => 'download',
    'naik' => 'upload',
    'fail' => 'file',
    'baru' => 'new',
    'baharu' => 'new',
    'Lihat' => 'View',
    'Sunting' => 'Edit',
    'Butiran' => 'Details',
    'Maklumat' => 'Information',
    'Tetapan' => 'Settings',
    'Log' => 'Log',
    'Masuk' => 'Login',
    'Keluar' => 'Logout',
    'Lupa' => 'Forgot',
    'Set' => 'Set',
    'Semula' => 'Reset',
    'Kata Laluan' => 'Password',
    'Adakah' => 'Are',
    'anda' => 'you',
    'pasti' => 'sure',
    'mahu' => 'want to',
    'meluluskan' => 'approve',
    'menolak' => 'reject',
    'memadamkan' => 'delete',
    'menggantung' => 'suspend',
    'ini' => 'this',
    'ke' => 'to',
    'kepada' => 'to',
    'nilai' => 'values',
    'lalai' => 'default',
    'semua' => 'all',
    'tetapan' => 'settings',
];

function guessEnglish(string $ms, array $wordMap, array $enJson): string
{
    if (isset($enJson[$ms])) {
        return $enJson[$ms];
    }
    if (str_ends_with($ms, ':')) {
        $base = rtrim($ms, ':');
        if (isset($enJson[$base.':'])) {
            return $enJson[$base.':'];
        }
        if (isset($enJson[$base])) {
            return $enJson[$base].':';
        }
    }

    $en = $ms;
    foreach ($wordMap as $bm => $enWord) {
        $en = preg_replace('/\b'.preg_quote($bm, '/').'\b/u', $enWord, $en);
    }

    return $en;
}

$added = 0;
foreach ($missing as $key) {
    if (isset($extra[$key])) {
        continue;
    }
    $extra[$key] = guessEnglish($key, $wordMap, $enJson);
    $added++;
}

file_put_contents($extraPath, json_encode($extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Added {$added} auto-translated keys; total " . count($extra) . "\n";
