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