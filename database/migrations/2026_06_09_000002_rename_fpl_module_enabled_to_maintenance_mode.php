<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        $legacy = Setting::where('key', 'fpl_module_enabled')->first();

        if ($legacy) {
            $legacy->delete();
        }

        // Keep FPL hidden by default after this fix.
        $maintenanceValue = 'true';

        Setting::updateOrCreate(
            ['key' => 'fpl_maintenance_mode'],
            [
                'value' => $maintenanceValue,
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => 'Hide Fantasy Premier League module across the website',
            ]
        );

        Setting::clearCache();
        Cache::forget('setting_fpl_module_enabled');
        Cache::forget('setting_fpl_maintenance_mode');
    }

    public function down(): void
    {
        $maintenance = Setting::where('key', 'fpl_maintenance_mode')->first();

        if ($maintenance) {
            $wasInMaintenance = filter_var($maintenance->value, FILTER_VALIDATE_BOOLEAN);

            Setting::updateOrCreate(
                ['key' => 'fpl_module_enabled'],
                [
                    'value' => $wasInMaintenance ? 'false' : 'true',
                    'type' => 'boolean',
                    'group' => 'maintenance',
                    'description' => 'Enable or disable Fantasy Premier League module across the website',
                ]
            );

            $maintenance->delete();
        }

        Setting::clearCache();
    }
};
