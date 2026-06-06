<?php

/**
 * Comprehensive i18n audit v2 — blades, controllers, JS, lang parity.
 * php scripts/scan-full-i18n-v2.php [--verbose]
 */

$base = dirname(__DIR__);
$verbose = in_array('--verbose', $argv ?? [], true);

$enJson = json_decode(file_get_contents($base . '/resources/lang/en.json'), true) ?: [];
$msFlash = require $base . '/resources/lang/ms/flash.php';
$enFlash = require $base . '/resources/lang/en/flash.php';
$msClient = require $base . '/resources/lang/ms/client_messages.php';
$enClient = require $base . '/resources/lang/en/client_messages.php';
$msAdmin = require $base . '/resources/lang/ms/admin.php';
$enAdmin = require $base . '/resources/lang/en/admin.php';

function flatKeys(array $a, string $p = ''): array
{
    $out = [];
    foreach ($a as $k => $v) {
        $key = $p === '' ? (string) $k : "{$p}.{$k}";
        $out = is_array($v) ? $out + flatKeys($v, $key) : $out + [$key => $v];
    }

    return $out;
}

$enAdminFlat = flatKeys($enAdmin);

$issues = [
    'invalid_wrapper' => [],
    'unwrapped_label' => [],
    'hardcoded_visible' => [],
    'js_hardcoded' => [],
    'section_title' => [],
    'placeholder_title' => [],
    'missing_en_json' => [],
    'missing_admin_en' => [],
    'controller_flash' => [],
    'controller_errors' => [],
    'flash_ms_en_missing' => [],
    'client_msg_missing_en' => [],
];

$viewDirs = [
    $base . '/resources/views/client',
    $base . '/resources/views/admin',
    $base . '/resources/views/layouts',
    $base . '/resources/views/errors',
    $base . '/resources/views/emails',
];

$malayWord = '/\b(sila|anda|pesanan|produk|perkhidmatan|penjual|pelanggan|alamat|simpan|padam|batal|kembali|tambah|edit|lihat|maklumat|berjaya|gagal|menunggu|aktif|tidak|semua|baru|nama|harga|stok|video|artikel|refund|ulasan|log|masuk|keluar|pentadbir|penghantaran|bil|troli|kegemaran|ringkasan|pratonton|contoh|masukkan|nota|status|tarikh|jumlah|nombor|pilih|muat|naik|hantar|cetak|eksport|import|tetapan|kategori|penapis|carian|sort|terbaru|lama|kosong|ralat|amaran|pengesahan|terima|kasih|gooners|arsenal|komuniti)\b/ui';

foreach ($viewDirs as $root) {
    if (! is_dir($root) && ! is_file($root)) {
        continue;
    }
    $iter = is_file($root)
        ? [$root]
        : new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($iter as $file) {
        $path = is_string($file) ? $file : $file->getPathname();
        if (! str_ends_with($path, '.blade.php')) {
            continue;
        }
        $rel = str_replace('\\', '/', substr($path, strlen($base) + 1));
        foreach (file($path) as $n => $line) {
            $ln = $n + 1;
            $trim = trim($line);

            if (preg_match_all("/__\('([^']+)'\)|__\(\"([^\"]+)\"\)/", $line, $m)) {
                foreach (array_merge($m[1], $m[2]) as $key) {
                    if ($key === '') {
                        continue;
                    }
                    if (preg_match('/^(target=|class=|rel=|href=|id=|style=)/', $key)) {
                        $issues['invalid_wrapper'][] = compact('rel', 'ln', 'key');
                    } elseif (str_starts_with($key, 'admin.')) {
                        if (! isset($enAdminFlat[substr($key, 6)])) {
                            $issues['missing_admin_en'][$key] = ($issues['missing_admin_en'][$key] ?? 0) + 1;
                        }
                    } elseif (! str_contains($key, '.') && ! str_starts_with($key, 'client_messages.') && ! str_starts_with($key, 'flash.') && ! isset($enJson[$key])) {
                        $issues['missing_en_json'][$key] = ($issues['missing_en_json'][$key] ?? 0) + 1;
                    }
                }
            }

            if (preg_match("/@section\('title',\s*'([^'{]+)'/", $line, $tm) && ! preg_match('/__\(/', $line)) {
                $issues['section_title'][] = ['file' => $rel, 'line' => $ln, 'text' => trim($tm[1])];
            }

            if (preg_match('/placeholder="([^{"]{4,})"/', $line, $pm) && ! preg_match('/__\(|trans\(/', $line)) {
                $issues['placeholder_title'][] = ['file' => $rel, 'line' => $ln, 'text' => $pm[1]];
            }
            if (preg_match('/title="([^{"]{4,})"/', $line, $tm2) && ! preg_match('/__\(|trans\(|{{/', $line)) {
                $issues['placeholder_title'][] = ['file' => $rel, 'line' => $ln, 'text' => 'title:' . $tm2[1]];
            }

            if (preg_match('/alert\(\s*\'([^\']+)\'/', $line, $am) && ! preg_match('/clientMessages|adminMessages|__\(|@json/', $line)) {
                $issues['js_hardcoded'][] = ['file' => $rel, 'line' => $ln, 'text' => $am[1]];
            }
            if (preg_match('/confirm\(\s*\'([^\']+)\'/', $line, $cm) && ! preg_match('/@json|__\(|adminMessages|clientMessages/', $line)) {
                $issues['js_hardcoded'][] = ['file' => $rel, 'line' => $ln, 'text' => 'confirm:' . $cm[1]];
            }
            if (preg_match('/showValidationModal\(\s*\'([^\']+)\'/', $line, $vm) && ! preg_match('/@json|__\(|adminMessages/', $line)) {
                $issues['js_hardcoded'][] = ['file' => $rel, 'line' => $ln, 'text' => $vm[1]];
            }

            if (preg_match('/<\/strong>\s+([A-Za-zÀ-ÿ][^<{]{6,})/u', $line, $sm) && ! preg_match('/__\(|trans\(|@json\(/', $line)) {
                $issues['unwrapped_label'][] = ['file' => $rel, 'line' => $ln, 'text' => mb_substr(trim($sm[1]), 0, 90)];
            }

            // label/button/th/td visible text
            if (preg_match('/<(label|button|th|td|h[1-6]|p|span|a|li)[^>]*>\s*([A-Za-zÀ-ÿ][^<]{4,80})\s*</iu', $line, $vm)) {
                $text = trim($vm[2]);
                if (isHardcodedUi($text, $line)) {
                    $issues['hardcoded_visible'][] = ['file' => $rel, 'line' => $ln, 'text' => mb_substr($text, 0, 90)];
                }
            }
        }
    }
}

function isHardcodedUi(string $text, string $line): bool
{
    global $malayWord;
    if (preg_match('/__\(|trans\(|{{|@json|@section|@extends|@if|@foreach|\$loop|wire:/i', $line)) {
        return false;
    }
    if (preg_match('/^(true|false|null|\d|RM|#|@|&|\*|\||\+)/', $text)) {
        return false;
    }
    if (! preg_match($malayWord, $text) && ! preg_match('/\b(Create|Edit|Delete|Save|Cancel|Submit|Search|Filter|Export|Import|Status|Actions|Name|Email|Phone|Date|Total|View|Add|Update|Remove|Back|Next|Previous|All|None|Yes|No|Loading|Error|Success|Warning)\b/', $text)) {
        return false;
    }

    return strlen($text) >= 4 && strlen($text) <= 120;
}

// Controllers
foreach (glob($base . '/app/Http/Controllers/{Client,Admin}/*.php', GLOB_BRACE) as $cf) {
    $c = file_get_contents($cf);
    $bn = basename($cf);
    if (preg_match_all("/->with\(['\"]success['\"],\s*'([^'\"\\\\]{6,})'/", $c, $m)) {
        foreach ($m[1] as $msg) {
            if (! str_contains($msg, '__') && ! str_contains($msg, 'client_messages') && ! str_contains($msg, 'flash.')) {
                $issues['controller_flash'][] = ['file' => $bn, 'msg' => mb_substr($msg, 0, 100)];
            }
        }
    }
    if (preg_match_all("/->with\(['\"]error['\"],\s*'([^'\"\\\\]{6,})'/", $c, $m)) {
        foreach ($m[1] as $msg) {
            if (! str_contains($msg, '__') && ! str_contains($msg, 'client_messages') && ! str_contains($msg, 'flash.')) {
                $issues['controller_flash'][] = ['file' => $bn, 'msg' => mb_substr($msg, 0, 100)];
            }
        }
    }
    if (preg_match_all("/withErrors\(\[[^\]]*'([^']+)'\s*=>\s*'([^'\"]{6,})'/", $c, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            if (! str_contains($match[2], '__')) {
                $issues['controller_errors'][] = ['file' => $bn, 'field' => $match[1], 'msg' => mb_substr($match[2], 0, 100)];
            }
        }
    }
}

foreach (array_keys($msFlash) as $k) {
    if (! isset($enFlash[$k])) {
        $issues['flash_ms_en_missing'][] = $k;
    }
}
foreach (array_keys($msClient) as $k) {
    if (! isset($enClient[$k])) {
        $issues['client_msg_missing_en'][] = $k;
    }
}

// Output
echo "=== MyGooners i18n Scan v2 ===\n\n";
$counts = array_map('count', $issues);
foreach ($counts as $k => $v) {
    echo str_pad($k . ':', 28) . $v . "\n";
}

function printGroup(string $title, array $items, int $limit = 40): void
{
    echo "\n--- {$title} (" . count($items) . ") ---\n";
    $shown = 0;
    $byFile = [];
    foreach ($items as $i) {
        $f = $i['file'] ?? ($i['key'] ?? 'n/a');
        $byFile[$f][] = $i;
    }
    foreach ($byFile as $file => $group) {
        if ($shown >= $limit) {
            echo "  ... and more\n";
            break;
        }
        echo "  {$file}\n";
        foreach (array_slice($group, 0, 5) as $it) {
            $line = isset($it['line']) ? "L{$it['line']}: " : '';
            $text = $it['text'] ?? $it['msg'] ?? $it['key'] ?? json_encode($it);
            echo "    {$line}{$text}\n";
        }
        $shown++;
    }
}

printGroup('Invalid __() wrappers', $issues['invalid_wrapper'], 10);
printGroup('Section titles without __()', $issues['section_title'], 30);
printGroup('placeholder/title hardcoded', $issues['placeholder_title'], 30);
printGroup('JS alert/confirm hardcoded', $issues['js_hardcoded'], 30);
printGroup('Text after </strong>', $issues['unwrapped_label'], 30);
printGroup('Visible UI hardcoded', $issues['hardcoded_visible'], 50);
printGroup('__() missing en.json', array_map(fn ($k, $c) => ['key' => $k, 'count' => $c], array_keys($issues['missing_en_json']), $issues['missing_en_json']), 30);
printGroup('admin.* missing EN', array_map(fn ($k, $c) => ['key' => $k, 'count' => $c], array_keys($issues['missing_admin_en']), $issues['missing_admin_en']), 20);
printGroup('Controller flash hardcoded', $issues['controller_flash'], 30);
printGroup('Controller withErrors hardcoded', $issues['controller_errors'], 30);
printGroup('flash.php missing EN', array_map(fn ($k) => ['key' => $k], $issues['flash_ms_en_missing']), 20);
printGroup('client_messages missing EN', array_map(fn ($k) => ['key' => $k], $issues['client_msg_missing_en']), 10);

file_put_contents(
    $base . '/storage/app/i18n-scan-v2-report.txt',
    json_encode($issues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);
echo "\nFull JSON report: storage/app/i18n-scan-v2-report.txt\nDone.\n";
