<?php

/**
 * Extract flash strings from Client controllers and build client_messages lang files.
 * Run: php scripts/generate-client-messages.php
 */

$base = dirname(__DIR__);
$controllers = glob($base . '/app/Http/Controllers/Client/*.php');
$strings = [];

foreach ($controllers as $file) {
    $content = file_get_contents($file);
    if (preg_match_all("/->with\\('(?:success|error)',\\s*'([^'\\\\]*(?:\\\\.[^'\\\\]*)*)'\\)/", $content, $m)) {
        foreach ($m[1] as $s) {
            $s = stripcslashes($s);
            if ($s !== '' && ! str_contains($s, '$')) {
                $strings[$s] = true;
            }
        }
    }
    if (preg_match_all('/->with\(\'(?:success|error)\',\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"\)/', $content, $m)) {
        foreach ($m[1] as $s) {
            $s = stripcslashes($s);
            if ($s !== '' && ! str_contains($s, '$')) {
                $strings[$s] = true;
            }
        }
    }
}

$enJson = json_decode(file_get_contents($base . '/resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);

$ms = [];
$en = [];
$i = 0;
foreach (array_keys($strings) as $malay) {
    $key = 'msg_' . substr(md5($malay), 0, 12);
    while (isset($ms[$key])) {
        $key = 'msg_' . substr(md5($malay . (++$i)), 0, 12);
    }
    $ms[$key] = $malay;
    $en[$key] = $enJson[$malay] ?? $malay;
}

ksort($ms);
$export = function (array $arr): string {
    $lines = ["<?php\n\nreturn ["];
    foreach ($arr as $k => $v) {
        $lines[] = '    ' . var_export($k, true) . ' => ' . var_export($v, true) . ',';
    }
    $lines[] = "];\n";
    return implode("\n", $lines);
};

file_put_contents($base . '/resources/lang/ms/client_messages.php', $export($ms));
file_put_contents($base . '/resources/lang/en/client_messages.php', $export($en));
echo 'Wrote ' . count($ms) . " client_messages keys\n";
