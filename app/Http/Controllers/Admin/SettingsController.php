<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get();
        
        // Group settings by their group
        $groupedSettings = $settings->groupBy('group');
        
        return view('admin.settings.index', compact('groupedSettings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|in:string,boolean,integer,json',
            'settings.*.group' => 'required|string',
            'settings.*.description' => 'nullable|string'
        ]);

        foreach ($request->settings as $settingData) {
            Setting::set(
                $settingData['key'],
                $settingData['value'],
                $settingData['type'],
                $settingData['group'],
                $settingData['description'] ?? null
            );
        }

        // Clear all settings cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Tetapan berjaya dikemas kini.');
    }

    /**
     * Reset settings to default values
     */
    public function reset()
    {
        // Clear existing settings
        Setting::truncate();
        
        // Create default settings
        $this->createDefaultSettings();
        
        // Clear cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Tetapan telah direset kepada nilai lalai.');
    }

    /**
     * Create default settings
     */
    private function createDefaultSettings()
    {
        $defaultSettings = [
            [
                'key' => 'stripe_payment_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => 'Enable or disable Stripe payment method visibility'
            ],
            [
                'key' => 'toyyibpay_payment_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => 'Enable or disable ToyyibPay payment method visibility'
            ],
            [
                'key' => 'site_name',
                'value' => 'MyGooners',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Website name'
            ],
            [
                'key' => 'site_description',
                'value' => 'Your trusted marketplace for products and services',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Website description'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Enable maintenance mode'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            Setting::create($setting);
        }
    }
} 