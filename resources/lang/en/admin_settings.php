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
        'fpl_module_enabled' => 'Fantasy Premier League Module',
    ],
    'descriptions' => [
        'stripe_payment_enabled' => 'Enable or disable Stripe payment method visibility',
        'toyyibpay_payment_enabled' => 'Enable or disable ToyyibPay payment method visibility',
        'site_name' => 'Website name displayed to users',
        'site_description' => 'Short description of the website',
        'maintenance_mode' => 'Enable maintenance mode to restrict user access',
        'fpl_module_enabled' => 'Temporarily disable the Fantasy Premier League module across checkout, orders, emails, and invoices',
    ],
];
