<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic order status updates
Schedule::command('orders:auto-mark-delivered')
    ->daily()
    ->at('09:00')
    ->description('Automatically mark shipped orders as delivered after 7 days');

// Schedule automatic cancellation of pending orders
Schedule::command('orders:auto-cancel-pending')
    ->hourly()
    ->description('Automatically cancel orders that have been pending for more than 24 hours without payment');
