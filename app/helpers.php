<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     */
    function setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('malayDiffForHumans')) {
    /**
     * Get human-readable time difference in Malay
     */
    function malayDiffForHumans($date)
    {
        $diff = $date->diffForHumans();
        
        // Translate common time phrases to Malay
        $translations = [
            'second' => 'saat',
            'seconds' => 'saat',
            'minute' => 'minit',
            'minutes' => 'minit',
            'hour' => 'jam',
            'hours' => 'jam',
            'day' => 'hari',
            'days' => 'hari',
            'week' => 'minggu',
            'weeks' => 'minggu',
            'month' => 'bulan',
            'months' => 'bulan',
            'year' => 'tahun',
            'years' => 'tahun',
            'ago' => 'yang lalu',
            'from now' => 'dari sekarang',
            'just now' => 'baru sahaja',
        ];
        
        foreach ($translations as $english => $malay) {
            $diff = str_replace($english, $malay, $diff);
        }
        
        return $diff;
    }
} 