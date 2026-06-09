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

if (!function_exists('fpl_module_enabled')) {
    function fpl_module_enabled(): bool
    {
        return ! (bool) setting('fpl_maintenance_mode', true);
    }
}

if (!function_exists('fpl_validation_rules')) {
    function fpl_validation_rules(): array
    {
        if (!fpl_module_enabled()) {
            return [];
        }

        return [
            'fpl_manager_name' => 'required|string|max:255',
            'fpl_team_name' => 'required|string|max:255',
        ];
    }
}

if (!function_exists('fpl_order_fields')) {
    function fpl_order_fields(?string $managerName = null, ?string $teamName = null): array
    {
        if (!fpl_module_enabled()) {
            return [
                'fpl_manager_name' => null,
                'fpl_team_name' => null,
            ];
        }

        return [
            'fpl_manager_name' => $managerName,
            'fpl_team_name' => $teamName,
        ];
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