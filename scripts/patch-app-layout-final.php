<?php

/**
 * Safe one-shot patch for layouts/app.blade.php after git restore.
 * Never wraps CSS/JS/PHP — UI strings and chatbot only.
 */
$path = dirname(__DIR__) . '/resources/views/layouts/app.blade.php';
$content = file_get_contents($path);

// --- i18n: title, meta, nav, footer (string replacements only) ---
$map = [
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
    'placeholder="Masukkan alamat emel anda"' => 'placeholder="{{ __(\'Masukkan alamat emel anda\') }}"',
    '<h3 class="text-2xl font-bold text-white mb-2">Kekal Terkini, Gooner!</h3>' => '<h3 class="text-2xl font-bold text-white mb-2">{{ __(\'Kekal Terkini, Gooner!\') }}</h3>',
    '<p class="text-gray-300 mb-6">Dapatkan berita Arsenal terkini, kandungan eksklusif, dan kemas kini komuniti terus ke peti mel anda.</p>' => '<p class="text-gray-300 mb-6">{{ __(\'Dapatkan berita Arsenal terkini, kandungan eksklusif, dan kemas kini komuniti terus ke peti mel anda.\') }}</p>',
    '<span x-show="!loading">Langgan</span>' => '<span x-show="!loading">{{ __(\'Langgan\') }}</span>',
    '<span x-show="!loading">Berhenti Langgan</span>' => '<span x-show="!loading">{{ __(\'Berhenti Langgan\') }}</span>',
    "Memproses...\n                                </span>\n                            </button>\n                            \n                            <!-- Unsubscribe" => "{{ __('Memproses...') }}\n                                </span>\n                            </button>\n                            \n                            <!-- Unsubscribe",
    'Berita terkini' => "{{ __('Berita terkini') }}",
    'Kandungan eksklusif' => "{{ __('Kandungan eksklusif') }}",
    'Tiada spam, berhenti melanggan bila-bila masa' => "{{ __('Tiada spam, berhenti melanggan bila-bila masa') }}",
    '<span class="ml-2 text-xl font-bold">MyGooners</span>' => '<span class="ml-2 text-xl font-bold">{{ __(\'MyGooners\') }}</span>',
    '<h3 class="text-lg font-semibold mb-4">Kandungan</h3>' => '<h3 class="text-lg font-semibold mb-4">{{ __(\'Kandungan\') }}</h3>',
    '<h3 class="text-lg font-semibold mb-4">Komuniti</h3>' => '<h3 class="text-lg font-semibold mb-4">{{ __(\'Komuniti\') }}</h3>',
    '<h3 class="text-lg font-semibold mb-4 mt-8">Berhubung</h3>' => '<h3 class="text-lg font-semibold mb-4 mt-8">{{ __(\'Berhubung\') }}</h3>',
    'Berita Terkini</a>' => "{{ __('Berita Terkini') }}</a>",
    'Podcast Video</a>' => "{{ __('Podcast Video') }}</a>",
    'Laporan Perlawanan</a>' => "{{ __('Laporan Perlawanan') }}</a>",
    'Berita Pemindahan</a>' => "{{ __('Berita Pemindahan') }}</a>",
    'Sertai Komuniti</a>' => "{{ __('Sertai Komuniti') }}</a>",
    // mobile nav
    "                                Utama\n                            </a>\n                            <a href=\"{{ route('blog.index') }}\"" => "                                {{ __('Utama') }}\n                            </a>\n                            <a href=\"{{ route('blog.index') }}\"",
    "                                Berita\n                            </a>\n                            <a href=\"{{ route('videos.index') }}\"" => "                                {{ __('Berita') }}\n                            </a>\n                            <a href=\"{{ route('videos.index') }}\"",
    "                                Video\n                            </a>\n                            <a href=\"{{ route('services.index') }}\"" => "                                {{ __('Video') }}\n                            </a>\n                            <a href=\"{{ route('services.index') }}\"",
    "                                Komuniti\n                            </a>\n                            <a href=\"{{ route('shop.index') }}\"" => "                                {{ __('Komuniti') }}\n                            </a>\n                            <a href=\"{{ route('shop.index') }}\"",
    "                                Kedai Kami\n                            </a>\n                            \n                            <!-- Auth buttons" => "                                {{ __('Kedai Kami') }}\n                            </a>\n                            \n                            <!-- Auth buttons",
    "                                        Log Masuk\n                                    </a>\n                                    <a href=\"{{ route('register') }}\"" => "                                        {{ __('Log Masuk') }}\n                                    </a>\n                                    <a href=\"{{ route('register') }}\"",
    "                                        Sertai Kami\n                                    </a>\n                                </div>\n                            @endguest" => "                                        {{ __('Sertai Kami') }}\n                                    </a>\n                                </div>\n                            @endguest",
    '<span>Troli</span>' => '<span>{{ __(\'Troli\') }}</span>',
    "                                        Total: RM" => "                                        {{ __('Total:') }} RM",
    "                                            Panel Kawalan\n                                        </a>\n                                        <a href=\"{{ auth()->user()->is_seller" => "                                            {{ __('Panel Kawalan') }}\n                                        </a>\n                                        <a href=\"{{ auth()->user()->is_seller",
    "                                            Profil\n                                        </a>\n                                        <a href=\"{{ route('checkout.orders') }}\"" => "                                            {{ __('Profil') }}\n                                        </a>\n                                        <a href=\"{{ route('checkout.orders') }}\"",
    "                                            Pesanan Saya\n                                        </a>\n                                        <a href=\"{{ route('checkout.refunds') }}\"" => "                                            {{ __('Pesanan Saya') }}\n                                        </a>\n                                        <a href=\"{{ route('checkout.refunds') }}\"",
    "                                            Permohonan Refund\n                                        </a>\n                                        <a href=\"{{ route('favourites.index') }}\"" => "                                            {{ __('Permohonan Refund') }}\n                                        </a>\n                                        <a href=\"{{ route('favourites.index') }}\"",
    "                                            Kegemaran\n                                            @if(auth()->check())" => "                                            {{ __('Kegemaran') }}\n                                            @if(auth()->check())",
];
foreach ($map as $from => $to) {
    $content = str_replace($from, $to, $content);
}

// Footer: language switcher + copyright
$content = str_replace(
    <<<'BLADE'
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} MyGooners. Hak cipta terpelihara. Dibina dengan ❤️ untuk peminat Arsenal.
                    </p>
                </div>
BLADE,
    <<<'BLADE'
                <div class="border-t border-gray-800 mt-8 pt-8">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mb-6">
                        @include('client.partials.language-switcher')
                    </div>
                    <p class="text-gray-400 text-sm text-center">
                        © {{ date('Y') }} MyGooners. {{ __('Hak cipta terpelihara. Dibina dengan ❤️ untuk peminat Arsenal.') }}
                    </p>
                </div>
BLADE,
    $content
);

// Remove duplicate / legacy Alpine loaders
$content = str_replace("    <script src=\"//unpkg.com/alpinejs\" defer></script>\n    @stack('scripts')\n", "    @stack('scripts')\n", $content);
$content = str_replace("    <script defer src=\"https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js\"></script>\n    \n    <!-- Newsletter Form Script -->", "    <!-- Newsletter Form Script -->", $content);

// Chatbot UI i18n
$content = str_replace('<div x-data="faqChatbot()" x-init="init" class="fixed bottom-6 right-6 z-50" x-cloak>', '<div x-data="faqChatbot" x-init="init()" class="fixed bottom-6 right-6 z-50">', $content);
$content = str_replace('<h3 class="font-semibold">MyGooners Assistant</h3>', '<h3 class="font-semibold">{{ __(\'faq_chatbot.assistant_title\') }}</h3>', $content);
$content = str_replace('<p class="text-xs text-red-100">Pilih soalan di bawah</p>', '<p class="text-xs text-red-100">{{ __(\'faq_chatbot.assistant_subtitle\') }}</p>', $content);
$content = str_replace(
    '<p class="text-sm">Halo! Saya adalah pembantu MyGooners. Pilih salah satu soalan popular di bawah untuk mendapatkan jawapan tentang Arsenal, laman web ini, dan banyak lagi.</p>',
    '<p class="text-sm">{{ __(\'faq_chatbot.welcome_message\') }}</p>',
    $content
);
$content = str_replace('<div class="text-xs text-gray-500 mb-2">Soalan Popular:</div>', '<div class="text-xs text-gray-500 mb-2">{{ __(\'faq_chatbot.popular_questions\') }}</div>', $content);
$content = str_replace("                        Join komuniti\n                    </button>", "                        {{ __('Join komuniti') }}\n                    </button>", $content);
$content = str_replace("                        Beli barang\n                    </button>", "                        {{ __('Beli barang') }}\n                    </button>", $content);
$content = str_replace("                        Tentang Arsenal\n                    </button>", "                        {{ __('Tentang Arsenal') }}\n                    </button>", $content);

// Replace old faqChatbot script block with Alpine 3 registration
$oldFaqScript = <<<'SCRIPT'
    <!-- FAQ Chatbot Script -->
    <script>
        function faqChatbot() {
            return {
                isOpen: false,
                messages: [],
                isTyping: false,
                
                faqData: {
SCRIPT;

$newFaqBlock = <<<'SCRIPT'
    @php
        $faqChatbotI18n = [
            'keywords' => trans('faq_chatbot.keywords'),
            'greeting' => trans('faq_chatbot.greeting'),
            'thanks' => trans('faq_chatbot.thanks'),
            'goodbye' => trans('faq_chatbot.goodbye'),
            'default' => trans('faq_chatbot.default'),
        ];
    @endphp
    <!-- FAQ Chatbot Script -->
    <script>
        window.faqChatbotI18n = @json($faqChatbotI18n);
        document.addEventListener('alpine:init', () => {
            Alpine.data('faqChatbot', () => ({
                isOpen: false,
                messages: [],
                isTyping: false,
                faqData: window.faqChatbotI18n.keywords,

                init() {
                    const saved = localStorage.getItem('faqChatbotOpen');
                    this.isOpen = saved === 'true';
                },

                toggleChat() {
                    this.isOpen = !this.isOpen;
                    localStorage.setItem('faqChatbotOpen', this.isOpen);
                    if (this.isOpen) {
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                sendQuickMessage(message) {
                    if (!message.trim()) return;
                    this.messages.push({ id: Date.now(), type: 'user', content: message });
                    const userMessage = message.toLowerCase();
                    this.isTyping = true;
                    this.scrollToBottom();
                    setTimeout(() => {
                        this.isTyping = false;
                        this.messages.push({
                            id: Date.now(),
                            type: 'bot',
                            content: this.getBotResponse(userMessage),
                        });
                        this.scrollToBottom();
                    }, 1000);
                },

                getBotResponse(message) {
                    for (const keyword in this.faqData) {
                        if (message.includes(keyword)) {
                            return this.faqData[keyword];
                        }
                    }
                    const i18n = window.faqChatbotI18n;
                    if (message.includes('halo') || message.includes('hello') || message.includes('hi')) {
                        return i18n.greeting;
                    }
                    if (message.includes('terima kasih') || message.includes('thank you')) {
                        return i18n.thanks;
                    }
                    if (message.includes('bye') || message.includes('selamat tinggal')) {
                        return i18n.goodbye;
                    }
                    return i18n.default;
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messageContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },
            }));
        });
    </script>
    <!-- MARKER_REMOVE_OLD_FAQ -->
    <script>
        function __removedFaqChatbot() {
            return {
                isOpen: false,
                messages: [],
                isTyping: false,
                faqData: {
SCRIPT;

if (strpos($content, $oldFaqScript) === false) {
    echo "Warning: old FAQ script marker not found\n";
} else {
    $content = str_replace($oldFaqScript, $newFaqBlock, $content);
    // Remove remainder of old function through closing script tag before navbar
    $content = preg_replace(
        '#<!-- MARKER_REMOVE_OLD_FAQ -->\s*<script>\s*function __removedFaqChatbot\(\)[\s\S]*?</script>\s*\n\s*<!-- Navbar scroll effect script -->#',
        "\n    <!-- Navbar scroll effect script -->",
        $content,
        1
    );
}

// Newsletter modal i18n
$content = str_replace("window.newsletterModal.showError('Ralat sistem. Sila cuba lagi.');", "window.newsletterModal.showError(@json(__('faq_chatbot.system_error')));", $content);
$content = str_replace("window.newsletterModal.showError('Sila masukkan alamat emel anda.');", "window.newsletterModal.showError(@json(__('Sila masukkan alamat emel anda.')));", $content);
$content = str_replace("this.showModal('Berjaya!', message, 'success');", "this.showModal(@json(__('faq_chatbot.modal_success')), message, 'success');", $content);
$content = str_replace("this.showModal('Perhatian', message, 'error');", "this.showModal(@json(__('faq_chatbot.modal_attention')), message, 'error');", $content);
$content = str_replace("                        OK\n                    </button>", "                        {{ __('OK') }}\n                    </button>", $content);

// Single Alpine 3 at end (before favourites)
$content = str_replace(
    "    <!-- Favourites Script -->",
    "    <script defer src=\"https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js\"></script>\n\n    <!-- Favourites Script -->",
    $content
);

// Safety: strip any accidental __() in style/script blocks
$content = preg_replace('/\{\{\ __\(\'display:[^\']+\'\)\ \}\}/', '', $content);
$content = preg_replace('/\{\{\ __\(\'return \$active\'\)\ \}\}/', 'return $active', $content);

if (str_contains($content, "{{ __('display:")) {
    echo "ERROR: corrupted __() wrappers still present — aborting\n";
    exit(1);
}

file_put_contents($path, $content);
echo "Patched app.blade.php successfully\n";
