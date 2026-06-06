<?php

/**
 * Full i18n audit: client + admin views, layouts, controllers.
 * Run: php scripts/scan-full-i18n.php [--json]
 */

$base = dirname(__DIR__);
$jsonOut = in_array('--json', $argv ?? [], true);

$viewRoots = [
    'client' => $base . '/resources/views/client',
    'admin' => $base . '/resources/views/admin',
    'layouts' => $base . '/resources/views/layouts',
];

$enJson = json_decode(file_get_contents($base . '/resources/lang/en.json'), true) ?: [];
$msAdmin = file_exists($base . '/resources/lang/ms/admin.php') ? require $base . '/resources/lang/ms/admin.php' : [];
$enAdmin = file_exists($base . '/resources/lang/en/admin.php') ? require $base . '/resources/lang/en/admin.php' : [];
$msClient = file_exists($base . '/resources/lang/ms/client_messages.php') ? require $base . '/resources/lang/ms/client_messages.php' : [];
$enClient = file_exists($base . '/resources/lang/en/client_messages.php') ? require $base . '/resources/lang/en/client_messages.php' : [];

$report = [
    'invalid_i18n_wrappers' => [],
    'unwrapped_after_strong' => [],
    'hardcoded_blade_text' => [],
    'missing_en_json_keys' => [],
    'missing_admin_en_keys' => [],
    'missing_client_messages_en' => [],
    'controller_hardcoded' => [],
    'stats' => ['files' => 0, '__(calls' => 0],
];

function flattenKeys(array $arr, string $prefix = ''): array
{
    $keys = [];
    foreach ($arr as $k => $v) {
        $key = $prefix === '' ? (string) $k : "{$prefix}.{$k}";
        if (is_array($v)) {
            $keys = array_merge($keys, flattenKeys($v, $key));
        } else {
            $keys[$key] = $v;
        }
    }

    return $keys;
}

$enAdminFlat = flattenKeys($enAdmin);

foreach ($viewRoots as $area => $root) {
    $files = is_file($root) ? [$root] : iterator_to_array(
        new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root)),
        false
    );
    foreach ($files as $file) {
        if (! is_string($file)) {
            $file = $file->getPathname();
        }
        if (! str_ends_with($file, '.blade.php')) {
            continue;
        }
        $rel = str_replace('\\', '/', substr($file, strlen($base) + 1));
        $lines = file($file);
        $report['stats']['files']++;

        foreach ($lines as $num => $line) {
            $ln = $num + 1;

            if (preg_match_all("/__\('([^']+)'\)|__\(\"([^\"]+)\"\)/", $line, $m)) {
                foreach (array_merge($m[1], $m[2]) as $key) {
                    if ($key === '') {
                        continue;
                    }
                    $report['stats']['__(calls']++;
                    if (preg_match('/^(target=|class=|rel=|href=|id=)/', $key)) {
                        $report['invalid_i18n_wrappers'][] = ['file' => $rel, 'line' => $ln, 'key' => $key];
                    } elseif (! str_contains($key, '.') && ! isset($enJson[$key]) && $area !== 'admin') {
                        if (! str_starts_with($key, 'client_messages.') && ! str_starts_with($key, 'admin.')) {
                            $report['missing_en_json_keys'][$key] = ($report['missing_en_json_keys'][$key] ?? 0) + 1;
                        }
                    } elseif (str_starts_with($key, 'admin.') && $area === 'admin') {
                        $sub = substr($key, 6);
                        if (! isset($enAdminFlat[$sub])) {
                            $report['missing_admin_en_keys'][$key] = ($report['missing_admin_en_keys'][$key] ?? 0) + 1;
                        }
                    }
                }
            }

            if (preg_match_all("/trans\('([^']+)'/", $line, $tm)) {
                foreach ($tm[1] as $tkey) {
                    // trans keys validated separately via lang files
                }
            }

            if (preg_match('/<\/strong>\s+([A-Za-zÀ-ÿ][^<{]{8,})/u', $line, $sm)) {
                if (! preg_match('/__\(|trans\(|@json\(/', $line) || preg_match('/<\/strong>\s+[A-Za-z]/u', $line)) {
                    $tail = trim($sm[1]);
                    if (! preg_match('/^{{|@|</', $tail) && preg_match('/[a-zA-Z]{4,}/', $tail)) {
                        $report['unwrapped_after_strong'][] = [
                            'file' => $rel,
                            'line' => $ln,
                            'text' => mb_substr($tail, 0, 80),
                        ];
                    }
                }
            }

            if (preg_match('/>\s*([A-Za-zÀ-ÿ][^<>{}\n]{6,120})\s*</u', $line, $tm2)) {
                $text = trim($tm2[1]);
                if (shouldFlagHardcoded($text, $line)) {
                    $report['hardcoded_blade_text'][] = [
                        'file' => $rel,
                        'line' => $ln,
                        'text' => mb_substr($text, 0, 100),
                    ];
                }
            }
        }
    }
}

// Controllers
$controllerDirs = [
    $base . '/app/Http/Controllers/Client',
    $base . '/app/Http/Controllers/Admin',
    $base . '/app/Http/Controllers',
];
foreach ($controllerDirs as $dir) {
    if (! is_dir($dir)) {
        continue;
    }
    foreach (glob($dir . '/*.php') as $cf) {
        $c = file_get_contents($cf);
        if (preg_match_all("/->with\(['\"]success['\"],\s*['\"]([^'\"]{8,})['\"]/", $c, $fm)) {
            foreach ($fm[1] as $msg) {
                if (preg_match('/[a-z]{3,}/i', $msg) && ! str_contains($msg, '__')) {
                    $report['controller_hardcoded'][] = ['file' => basename($cf), 'msg' => mb_substr($msg, 0, 80)];
                }
            }
        }
        if (preg_match_all("/->with\(['\"]error['\"],\s*['\"]([^'\"]{8,})['\"]/", $c, $fm)) {
            foreach ($fm[1] as $msg) {
                if (preg_match('/[a-z]{3,}/i', $msg)) {
                    $report['controller_hardcoded'][] = ['file' => basename($cf), 'msg' => mb_substr($msg, 0, 80)];
                }
            }
        }
    }
}

// client_messages key parity
foreach (array_keys($msClient) as $k) {
    if (! isset($enClient[$k])) {
        $report['missing_client_messages_en'][] = $k;
    }
}

function shouldFlagHardcoded(string $text, string $line): bool
{
    if (preg_match('/__\(|trans\(|{{|@json|@section|@extends|@if|@foreach|wire:|x-/i', $line)) {
        return false;
    }
    if (preg_match('/^(class|svg|path|http|GET|POST|true|false|\d|RM|\$|#)/i', $text)) {
        return false;
    }
    if (preg_match('/^[\d\s\.\+\-%:;,\(\)RM\$#]+$/u', $text)) {
        return false;
    }
    if (! preg_match('/[a-zA-Z]{4,}/', $text)) {
        return false;
    }
    // Malay/common UI words heuristic
    if (! preg_match('/\b(dan|atau|untuk|dengan|anda|sila|pesanan|produk|admin|refund|alamat|nama|harga|tiada|semua|baru|edit|padam|lihat|kembali|maklumat|berjaya|gagal|menunggu|aktif|video|artikel|perkhidmatan|penjual|pelanggan|stok|batal|simpan|masuk|log|ringkasan|pratonton|contoh|masukkan|portfolio|kelulusan|menjejak|penghantaran|emel|komuniti|gooners)\b/ui', $text)) {
        return false;
    }

    return true;
}

if ($jsonOut) {
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit(0);
}

echo "=== MyGooners Full i18n Scan ===\n\n";
echo 'Blade files scanned: ' . $report['stats']['files'] . "\n";
echo '__() calls found: ' . $report['stats']['__(calls'] . "\n\n";

echo '--- Invalid __() wrappers (target/class attrs): ' . count($report['invalid_i18n_wrappers']) . " ---\n";
foreach (array_slice($report['invalid_i18n_wrappers'], 0, 20) as $i) {
    echo "  {$i['file']}:{$i['line']}  __({$i['key']})\n";
}

echo "\n--- Text after </strong> not in __() (sample): " . count($report['unwrapped_after_strong']) . " ---\n";
$byFile = [];
foreach ($report['unwrapped_after_strong'] as $i) {
    $byFile[$i['file']][] = $i;
}
$n = 0;
foreach ($byFile as $file => $items) {
    if ($n >= 25) {
        break;
    }
    echo "  {$file}\n";
    foreach (array_slice($items, 0, 3) as $it) {
        echo "    L{$it['line']}: {$it['text']}\n";
    }
    $n++;
}

echo "\n--- Hardcoded Malay UI text in blades (heuristic): " . count($report['hardcoded_blade_text']) . " ---\n";
$byFile2 = [];
foreach ($report['hardcoded_blade_text'] as $i) {
    $byFile2[$i['file']][] = $i;
}
$n = 0;
foreach ($byFile2 as $file => $items) {
    if ($n >= 20) {
        break;
    }
    echo "  {$file} (" . count($items) . ")\n";
    foreach (array_slice($items, 0, 2) as $it) {
        echo "    L{$it['line']}: {$it['text']}\n";
    }
    $n++;
}

echo "\n--- __() keys missing from en.json (top 30 by usage): " . count($report['missing_en_json_keys']) . " unique ---\n";
arsort($report['missing_en_json_keys']);
foreach (array_slice($report['missing_en_json_keys'], 0, 30, true) as $k => $cnt) {
    echo "  [{$cnt}x] {$k}\n";
}

echo "\n--- admin.* keys missing EN: " . count($report['missing_admin_en_keys']) . " ---\n";
foreach (array_slice($report['missing_admin_en_keys'], 0, 15, true) as $k => $cnt) {
    echo "  [{$cnt}x] {$k}\n";
}

echo "\n--- client_messages missing EN: " . count($report['missing_client_messages_en']) . " ---\n";
foreach (array_slice($report['missing_client_messages_en'], 0, 15) as $k) {
    echo "  {$k}\n";
}

echo "\n--- Controller hardcoded flash (sample): " . count($report['controller_hardcoded']) . " ---\n";
foreach (array_slice($report['controller_hardcoded'], 0, 15) as $i) {
    echo "  {$i['file']}: {$i['msg']}\n";
}

echo "\nDone.\n";
