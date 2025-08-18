<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sertai Kami - MyGooners</title>
    <meta name="description" content="Sertai komuniti MyGooners">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="h-full bg-gray-50 font-sans antialiased" x-data="{ 
    showTermsModal: false, 
    showPrivacyModal: false 
}">
    <!-- Flash Messages - Floating Right Side -->
    <div class="fixed top-4 right-4 z-50">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button @click="show = false" class="inline-flex text-green-400 hover:text-green-600 focus:outline-none focus:text-green-600">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button @click="show = false" class="inline-flex text-red-400 hover:text-red-600 focus:outline-none focus:text-red-600">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative" style="background-image: url('{{ asset('images/hero-section.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="absolute inset-0 bg-black bg-opacity-75"></div>
        <div class="max-w-md w-full relative z-10">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center space-x-2 mb-4 pl-1 pt-1">
                    <a href="{{ route('home') }}" class="flex items-center text-gray-500 text-sm font-normal hover:text-gray-700 focus:outline-none" aria-label="Kembali ke Utama">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Back</span>
                    </a>
                </div>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Sertai komuniti Arsenal
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Menjadi sebahagian daripada keluarga MyGooners
                </p>
                
                <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Penuh</label>
                            <input id="name" 
                                   name="name" 
                                   type="text" 
                                   autocomplete="name" 
                                   required 
                                   value="{{ old('name') }}"
                                   class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('name') border-red-500 @enderror" 
                                   placeholder="Nama penuh anda">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Emel</label>
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   autocomplete="email" 
                                   required 
                                   value="{{ old('email') }}"
                                   class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('email') border-red-500 @enderror" 
                                   placeholder="emel@anda.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Kata Laluan</label>
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   required 
                                   class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('password') border-red-500 @enderror" 
                                   placeholder="Pilih kata laluan yang kuat">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Sahkan Kata Laluan</label>
                            <input id="password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   required 
                                   class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                                   placeholder="Sahkan kata laluan anda">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="terms" 
                               name="terms" 
                               type="checkbox" 
                               required
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="terms" class="ml-2 block text-sm text-gray-900">
                            Saya bersetuju dengan 
                            <button type="button" @click="showTermsModal = true" class="text-red-600 hover:text-red-500 font-medium underline">Syarat Perkhidmatan</button> 
                            dan 
                            <button type="button" @click="showPrivacyModal = true" class="text-red-600 hover:text-red-500 font-medium underline">Dasar Privasi</button>
                        </label>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-red-500 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            Cipta Akaun
                        </button>
                    </div>
                </form>
                <div class="flex items-center my-4">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="mx-3 text-gray-400 text-sm">Atau teruskan dengan</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>
                <div class="flex justify-center">
                    <a href="{{ route('login.google') }}"
                       class="flex items-center justify-center w-12 h-12 rounded-xl shadow border border-gray-200 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <svg class="w-6 h-6" viewBox="0 0 48 48"><g><path d="M44.5 20H24v8.5h11.7C34.1 33.9 29.6 37 24 37c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 6 .9 8.3 2.7l6.2-6.2C34.3 5.1 29.4 3 24 3 12.4 3 3 12.4 3 24s9.4 21 21 21c10.5 0 20-7.6 20-21 0-1.3-.1-2.7-.5-4z" fill="#FFC107"/><path d="M6.3 14.7l7 5.1C15.5 17.1 19.4 15 24 15c3.1 0 6 .9 8.3 2.7l6.2-6.2C34.3 5.1 29.4 3 24 3c-7.2 0-13 5.8-13 13 0 1.6.3 3.1.8 4.7z" fill="#FF3D00"/><path d="M24 45c5.4 0 10.3-1.8 14.1-4.9l-6.5-5.3C29.7 36.7 26.9 37.5 24 37.5c-5.6 0-10.1-3.1-12.4-7.6l-7 5.4C7.1 42.2 14.9 45 24 45z" fill="#4CAF50"/><path d="M44.5 20H24v8.5h11.7c-1.1 3.1-4.1 5.5-7.7 5.5-2.2 0-4.2-.7-5.7-2.1l-7 5.4C17.9 42.2 20.8 45 24 45c10.5 0 20-7.6 20-21 0-1.3-.1-2.7-.5-4z" fill="#1976D2"/></g></svg>
                    </a>
                </div>

                <div class="text-center mt-8">
                    <p class="text-sm text-gray-600">
                        Sudah mempunyai akaun? 
                        <a href="{{ route('login') }}" class="font-medium text-red-600 hover:text-red-500">
                            Log masuk di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms of Service Modal -->
    <div x-show="showTermsModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Syarat Perkhidmatan
                        </h3>
                        <button @click="showTermsModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto text-sm text-gray-700 space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">1. Penerimaan Syarat</h4>
                            <p>Dengan menggunakan platform MyGooners, anda bersetuju untuk mematuhi semua syarat dan terma yang dinyatakan di sini. Jika anda tidak bersetuju dengan mana-mana bahagian syarat ini, sila jangan gunakan perkhidmatan kami.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">2. Penggunaan Platform</h4>
                            <p>MyGooners adalah platform komuniti Arsenal yang menyediakan perkhidmatan marketplace untuk produk dan perkhidmatan berkaitan Arsenal. Pengguna boleh membeli, menjual, dan berinteraksi dalam komuniti yang selamat dan mesra.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">3. Akaun Pengguna</h4>
                            <p>Anda bertanggungjawab untuk mengekalkan kerahsiaan akaun anda dan kata laluan. Semua aktiviti yang berlaku di bawah akaun anda adalah tanggungjawab anda. Beritahu kami dengan segera jika anda mengesyaki sebarang penggunaan yang tidak dibenarkan.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">4. Kandungan Pengguna</h4>
                            <p>Pengguna bertanggungjawab untuk semua kandungan yang mereka muat naik, termasuk ulasan, gambar, dan maklumat produk. Kandungan mesti mematuhi garis panduan komuniti dan tidak boleh mengandungi bahan yang menyinggung, memfitnah, atau melanggar hak cipta.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">5. Transaksi dan Pembayaran</h4>
                            <p>Semua transaksi dijalankan melalui sistem pembayaran yang selamat. MyGooners bertindak sebagai perantara dan tidak bertanggungjawab untuk sebarang pertikaian antara pembeli dan penjual. Pengguna digalakkan untuk menyelesaikan sebarang isu secara aman.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">6. Penggantungan dan Penamatan</h4>
                            <p>Kami berhak untuk menggantung atau menamatkan akaun pengguna yang melanggar syarat perkhidmatan. Penggantungan boleh dilakukan tanpa notis awal jika terdapat pelanggaran serius terhadap garis panduan komuniti.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">7. Pindaan Syarat</h4>
                            <p>Kami berhak untuk mengubah suai syarat perkhidmatan pada bila-bila masa. Perubahan akan diberitahu kepada pengguna melalui platform atau emel. Penggunaan berterusan selepas perubahan dianggap sebagai penerimaan syarat baharu.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">8. Hubungi Kami</h4>
                            <p>Jika anda mempunyai sebarang pertanyaan mengenai syarat perkhidmatan, sila hubungi pasukan sokongan kami melalui emel atau borang hubungan yang disediakan di platform.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="showTermsModal = false" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div x-show="showPrivacyModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Dasar Privasi
                        </h3>
                        <button @click="showPrivacyModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto text-sm text-gray-700 space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">1. Maklumat Yang Kami Kumpul</h4>
                            <p>Kami mengumpul maklumat yang anda berikan secara langsung, seperti nama, alamat emel, dan maklumat profil. Kami juga mengumpul maklumat secara automatik melalui cookies dan teknologi serupa untuk meningkatkan pengalaman pengguna.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">2. Penggunaan Maklumat</h4>
                            <p>Maklumat yang dikumpul digunakan untuk menyediakan, mengekalkan, dan meningkatkan perkhidmatan kami; memproses transaksi; menghantar notifikasi penting; dan memberikan sokongan pelanggan. Kami tidak menjual, menyewa, atau berkongsi maklumat peribadi anda dengan pihak ketiga tanpa kebenaran anda.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">3. Keselamatan Data</h4>
                            <p>Kami melaksanakan langkah-langkah keselamatan teknikal dan organisasi yang sesuai untuk melindungi maklumat peribadi anda daripada akses, penggunaan, atau pendedahan yang tidak dibenarkan. Data anda dienkripsi semasa penghantaran dan penyimpanan.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">4. Cookies dan Teknologi Serupa</h4>
                            <p>Kami menggunakan cookies dan teknologi serupa untuk mengingati pilihan anda, memahami bagaimana anda menggunakan platform kami, dan menyesuaikan kandungan. Anda boleh mengawal penggunaan cookies melalui tetapan pelayar anda.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">5. Perkongsian Maklumat</h4>
                            <p>Kami mungkin berkongsi maklumat anda dalam situasi tertentu, seperti mematuhi undang-undang, melindungi hak dan keselamatan kami, atau dengan kebenaran anda. Kami tidak berkongsi maklumat peribadi untuk tujuan pemasaran tanpa kebenaran eksplisit.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">6. Hak Pengguna</h4>
                            <p>Anda mempunyai hak untuk mengakses, membetulkan, atau memadamkan maklumat peribadi anda. Anda juga boleh menarik balik kebenaran untuk pemprosesan data pada bila-bila masa. Untuk melaksanakan hak ini, sila hubungi pasukan sokongan kami.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">7. Penyimpanan Data</h4>
                            <p>Kami menyimpan maklumat peribadi anda selagi diperlukan untuk menyediakan perkhidmatan atau mematuhi kewajipan undang-undang. Apabila data tidak lagi diperlukan, kami akan memadamkannya dengan selamat atau menganonimkannya.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">8. Pindaan Dasar</h4>
                            <p>Kami mungkin mengemas kini dasar privasi ini dari semasa ke semasa. Perubahan ketara akan diberitahu kepada anda melalui platform atau emel. Kami menggalakkan anda untuk mengkaji dasar ini secara berkala.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">9. Hubungi Kami</h4>
                            <p>Jika anda mempunyai sebarang pertanyaan mengenai dasar privasi kami atau cara kami memproses maklumat peribadi anda, sila hubungi pegawai perlindungan data kami melalui emel atau borang hubungan yang disediakan.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="showPrivacyModal = false" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 