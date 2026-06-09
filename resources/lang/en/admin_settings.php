<?php

return [
    'groups' => [
        'payment' => 'Payment',
        'general' => 'General',
        'maintenance' => 'Maintenance Mode',
    ],
    'keys' => [
        'stripe_payment_enabled' => 'Stripe Payment',
        'toyyibpay_payment_enabled' => 'ToyyibPay Payment',
        'site_name' => 'Site Name',
        'site_description' => 'Site Description',
        'maintenance_mode' => 'Maintenance Mode',
        'fpl_maintenance_mode' => 'Fantasy Premier League Maintenance',
    ],
    'descriptions' => [
        'stripe_payment_enabled' => 'Enable or disable Stripe payment method visibility',
        'toyyibpay_payment_enabled' => 'Enable or disable ToyyibPay payment method visibility',
        'site_name' => 'Website name displayed to users',
        'site_description' => 'Short description of the website',
        'maintenance_mode' => 'Enable maintenance mode to restrict user access',
        'fpl_maintenance_mode' => 'When active, hides Fantasy Premier League from checkout, orders, emails, and invoices',
    ],
];
