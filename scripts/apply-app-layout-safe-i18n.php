<?php

/**
 * Apply user-visible __() wraps to layouts/app.blade.php only (never CSS/JS/PHP/Alpine).
 */
$path = dirname(__DIR__) . '/resources/views/layouts/app.blade.php';
$content = file_get_contents($path);

$replacements = [
    "@yield('title', 'MyGooners - Komuniti Peminat Arsenal')" => "@yield('title', __('MyGooners - Komuniti Peminat Arsenal'))",
    "@yield('meta_description', 'Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video, pasaran perkhidmatan, dan barangan eksklusif.')" => "@yield('meta_description', __('Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video, pasaran perkhidmatan, dan barangan eksklusif.'))",

    ">\n                            Utama\n                        </a>" => ">\n                            {{ __('Utama') }}\n                        </a>",
    ">\n                            Berita\n                        </a>" => ">\n                            {{ __('Berita') }}\n                        </a>",
    ">\n                            Video\n                        </a>" => ">\n                            {{ __('Video') }}\n                        </a>",
    ">\n                            Komuniti\n                        </a>" => ">\n                            {{ __('Komuniti') }}\n                        </a>",
    ">\n                            Kedai Kami\n                        </a>" => ">\n                            {{ __('Kedai Kami') }}\n                        </a>",

    'group-hover:text-red-600 transition-colors">Troli</span>' => 'group-hover:text-red-600 transition-colors">{{ __(\'Troli\') }}</span>',

    ">\n                                        Panel Kawalan\n                                    </a>" => ">\n                                        {{ __('Panel Kawalan') }}\n                                    </a>",
    ">\n                                        Profil\n                                    </a>" => ">\n                                        {{ __('Profil') }}\n                                    </a>",
    ">\n                                        Pesanan Saya\n                                    </a>" => ">\n                                        {{ __('Pesanan Saya') }}\n                                    </a>",
    ">\n                                        Permohonan Refund\n                                    </a>" => ">\n                                        {{ __('Permohonan Refund') }}\n                                    </a>",
    ">\n                                        Kegemaran\n                                        @if" => ">\n                                        {{ __('Kegemaran') }}\n                                        @if",
    ">\n                                        Alamat\n                                    </a>" => ">\n                                        {{ __('Alamat') }}\n                                    </a>",
    ">\n                                            Log Keluar\n                                        </button>" => ">\n                                            {{ __('Log Keluar') }}\n                                        </button>",

    'transition-colors">Log Masuk</a>' => 'transition-colors">{{ __(\'Log Masuk\') }}</a>',
    'transition-colors">Sertai Kami</a>' => 'transition-colors">{{ __(\'Sertai Kami\') }}</a>',

    '<div class="text-xs text-gray-500 mb-2">Soalan Popular:</div>' => '<div class="text-xs text-gray-500 mb-2">{{ __(\'faq_chatbot.popular_questions\') }}</div>',

    "class=\"px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors\">\n                        Join komuniti\n                    </button>" => "class=\"px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors\">\n                        {{ __('Join komuniti') }}\n                    </button>",
    "class=\"px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors\">\n                        Beli barang\n                    </button>" => "class=\"px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors\">\n                        {{ __('Beli barang') }}\n                    </button>",
    "class=\"px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors\">\n                        Tentang Arsenal\n                    </button>" => "class=\"px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors\">\n                        {{ __('Tentang Arsenal') }}\n                    </button>",

    "window.newsletterModal.showError('Ralat sistem. Sila cuba lagi.');" => 'window.newsletterModal.showError(@json(__(\'faq_chatbot.system_error\')));',
    "this.showModal('Berjaya!', message, 'success');" => 'this.showModal(@json(__(\'faq_chatbot.modal_success\')), message, \'success\');',
    "this.showModal('Perhatian', message, 'error');" => 'this.showModal(@json(__(\'faq_chatbot.modal_attention\')), message, \'error\');',
];

foreach ($replacements as $from => $to) {
    $content = str_replace($from, $to, $content);
}

// FAQ chatbot: replace faqData object (multiline)
$faqStart = "                faqData: {\n";
$faqEnd = "                },\n                \n                init()";
$startPos = strpos($content, $faqStart);
$endPos = strpos($content, $faqEnd);
if ($startPos !== false && $endPos !== false) {
    $content = substr($content, 0, $startPos)
        . "                faqData: @json(trans('faq_chatbot.keywords')),\n                \n                init() {"
        . substr($content, $endPos + strlen($faqEnd));
}

$botReturns = [
    "return 'Halo! Selamat datang ke MyGooners. Bagaimana saya boleh membantu anda hari ini?';" => 'return @json(__(\'faq_chatbot.greeting\'));',
    "return 'Sama-sama! Saya sentiasa di sini untuk membantu. Ada lagi yang anda ingin tahu?';" => 'return @json(__(\'faq_chatbot.thanks\'));',
    "return 'Selamat tinggal! Jangan lupa untuk kembali lagi ke MyGooners. COYG! 🔴';" => 'return @json(__(\'faq_chatbot.goodbye\'));',
    "return 'Maaf, saya tidak jumpa jawapan untuk pilihan itu. Sila pilih salah satu soalan popular di bawah. Saya boleh membantu dengan maklumat tentang Arsenal, cara guna laman web ini, dan banyak lagi!';" => 'return @json(__(\'faq_chatbot.default\'));',
];
foreach ($botReturns as $from => $to) {
    $content = str_replace($from, $to, $content);
}
// Fallback for mojibake goodbye string in git version
$content = preg_replace(
    "/return 'Selamat tinggal![^']+';/",
    'return @json(__(\'faq_chatbot.goodbye\'));',
    $content,
    1
);

// Language switcher in footer
$oldFooter = <<<'BLADE'
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400 text-sm">
BLADE;
$newFooter = <<<'BLADE'
                <div class="border-t border-gray-800 mt-8 pt-8">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mb-6">
                        @include('client.partials.language-switcher')
                    </div>
                    <p class="text-gray-400 text-sm text-center">
BLADE;
$content = str_replace($oldFooter, $newFooter, $content);

file_put_contents($path, $content);
echo "Applied safe i18n to app.blade.php\n";
