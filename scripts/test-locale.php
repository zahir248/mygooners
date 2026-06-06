<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['ms', 'en'] as $locale) {
    app()->setLocale($locale);
    echo "=== $locale ===\n";
    echo 'Berita Terkini: ' . __('Berita Terkini') . "\n";
    echo 'faq assistant: ' . trans('faq_chatbot.assistant_title') . "\n";
    echo 'Join komuniti: ' . __('Join komuniti') . "\n";
}
