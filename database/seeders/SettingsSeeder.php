<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
} 