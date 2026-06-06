<?php

/**
 * Malay => English for client site JSON i18n.
 */
$map = [
    // Layout nav & footer (high traffic)
    'Utama' => 'Home',
    'Berita' => 'News',
    'Video' => 'Videos',
    'Komuniti' => 'Community',
    'Kedai Kami' => 'Our Shop',
    'Log Masuk' => 'Log In',
    'Sertai Kami' => 'Join Us',
    'Troli' => 'Cart',
    'Log Keluar' => 'Log Out',
    'Panel Kawalan' => 'Dashboard',
    'Profil' => 'Profile',
    'Pesanan Saya' => 'My Orders',
    'Permohonan Refund' => 'Refund Requests',
    'Kegemaran' => 'Favourites',
    'Alamat' => 'Addresses',
    'Kandungan' => 'Content',
    'Berhubung' => 'Connect',
    'Berita Terkini' => 'Latest News',
    'Podcast Video' => 'Video Podcast',
    'Laporan Perlawanan' => 'Match Reports',
    'Berita Pemindahan' => 'Transfer News',
    'Sertai Komuniti' => 'Join the Community',
    'Kekal Terkini, Gooner!' => 'Stay Updated, Gooner!',
    'Langgan' => 'Subscribe',
    'Berhenti Langganan' => 'Unsubscribe',
    'Memproses...' => 'Processing...',
    'Berita terkini' => 'Latest news',
    'Kandungan eksklusif' => 'Exclusive content',
    'Tiada spam, berhenti melanggan bila-bila masa' => 'No spam, unsubscribe anytime',
    'Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video, pasaran perkhidmatan, dan barangan eksklusif.' => 'The best Arsenal fan community featuring the latest news, videos, a services marketplace, and exclusive merchandise.',
    '©' => '©',
    'Hak cipta terpelihara. Dibina dengan ❤️ untuk peminat Arsenal.' => 'All rights reserved. Built with ❤️ for Arsenal fans.',
    '← Kembali ke MyGooners' => '← Back to MyGooners',
    'Ingat saya' => 'Remember me',
    'Hak cipta terpelihara. Dibina dengan ?? untuk peminat Arsenal.' => 'All rights reserved. Built with ❤️ for Arsenal fans.',
    'Lupa kata laluan?' => 'Forgot password?',
    'Masukkan alamat emel anda' => 'Enter your email address',
    'Aduh! Ada yang tidak kena.' => 'Oops! Something went wrong.',
    'Sertai Kami - MyGooners' => 'Join Us - MyGooners',
    'Lupa Kata Laluan - MyGooners' => 'Forgot Password - MyGooners',
    'Reset Kata Laluan - MyGooners' => 'Reset Password - MyGooners',
    'Sertai komuniti MyGooners' => 'Join the MyGooners community',
    'Kembali ke Log Masuk' => 'Back to Log In',
    'Kembali ke Lupa Kata Laluan' => 'Back to Forgot Password',
    'Disenarai' => 'Listed',
    'Menandakan medan yang wajib diisi' => 'Marks required fields',
];

$extraFile = __DIR__ . '/client-translation-map-extra.php';
if (is_file($extraFile)) {
    return array_merge($map, require $extraFile);
}

return $map;
