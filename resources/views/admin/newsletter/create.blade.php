@extends('layouts.admin')

@section('title', 'Hantar Newsletter')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Hantar Newsletter</h1>
            <a href="{{ route('admin.newsletter.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Kembali
            </a>
        </div>

        <!-- Statistics Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-800">Newsletter akan dihantar kepada</p>
                    <p class="text-2xl font-semibold text-blue-900">{{ number_format($activeSubscribers) }} pelanggan aktif</p>
                </div>
            </div>
        </div>

        <!-- Newsletter Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Komposisi Newsletter</h2>
            </div>
            
            <form id="newsletterForm" action="{{ route('admin.newsletter.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="space-y-6">
                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subjek Email <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="subject" 
                               name="subject" 
                               value="{{ old('subject') }}"
                               required
                               maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('subject') border-red-500 @enderror"
                               placeholder="Contoh: Berita Arsenal Terkini - Minggu Ini">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Kandungan Newsletter <span class="text-red-500">*</span>
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="12"
                                  required
                                  minlength="10"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('content') border-red-500 @enderror"
                                  placeholder="Tulis kandungan newsletter anda di sini...">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <div class="mt-2 text-sm text-gray-500">
                            <p>💡 Tips untuk newsletter yang berkesan:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Mulakan dengan salam yang mesra</li>
                                <li>Kongsi berita Arsenal terkini</li>
                                <li>Sertakan pautan ke artikel atau video</li>
                                <li>Berikan maklumat tentang kemas kini komuniti</li>
                                <li>Akhiri dengan seruan untuk tindakan</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div>
                        <button type="button" 
                                onclick="openPreviewModal()"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Lihat Preview
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.newsletter.index') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="button" 
                                onclick="openSendModal()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            Hantar Newsletter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Preview Newsletter</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="previewContent" class="border rounded-lg p-6 bg-gray-50 max-h-96 overflow-y-auto">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="closePreviewModal()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Validation Modal -->
<div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900">Perhatian</h3>
                <div class="mt-2 px-7 py-3">
                    <p id="validationMessage" class="text-sm text-gray-500">
                        <!-- Validation message will be inserted here -->
                    </p>
                </div>
            </div>
            <div class="flex justify-center px-4 py-3">
                <button onclick="closeValidationModal()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Send Newsletter Modal -->
<div id="sendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900">Hantar Newsletter</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Adakah anda pasti untuk menghantar newsletter kepada <strong>{{ number_format($activeSubscribers) }} pelanggan aktif</strong>?
                    </p>
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">Perhatian:</p>
                                <ul class="mt-1 list-disc list-inside space-y-1">
                                    <li>Newsletter akan dihantar kepada semua pelanggan aktif</li>
                                    <li>Proses ini mungkin mengambil masa beberapa minit</li>
                                    <li>Pastikan kandungan adalah tepat sebelum dihantar</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 px-4 py-3">
                <button onclick="closeSendModal()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Batal
                </button>
                <button onclick="submitNewsletter()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Hantar Newsletter
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openPreviewModal() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    
    if (!subject || !content) {
        showValidationModal('Sila isi subjek dan kandungan terlebih dahulu.');
        return;
    }
    
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = `
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-4">
                <h4 class="font-semibold text-gray-900 text-lg mb-2">Subjek:</h4>
                <p class="text-gray-700 text-lg">${subject}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 text-lg mb-2">Kandungan:</h4>
                <div class="bg-white p-4 rounded-lg border">
                    <div class="text-gray-700 whitespace-pre-wrap leading-relaxed">${content}</div>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-blue-800">
                        <strong>Preview:</strong> Ini adalah bagaimana newsletter anda akan kelihatan kepada pelanggan.
                    </p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('previewModal').classList.remove('hidden');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

function openSendModal() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    
    if (!subject || !content) {
        showValidationModal('Sila isi subjek dan kandungan terlebih dahulu.');
        return;
    }
    
    document.getElementById('sendModal').classList.remove('hidden');
}

function closeSendModal() {
    document.getElementById('sendModal').classList.add('hidden');
}

function submitNewsletter() {
    document.getElementById('newsletterForm').submit();
}

function showValidationModal(message) {
    document.getElementById('validationMessage').textContent = message;
    document.getElementById('validationModal').classList.remove('hidden');
}

function closeValidationModal() {
    document.getElementById('validationModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreviewModal();
    }
});

document.getElementById('sendModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSendModal();
    }
});

document.getElementById('validationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeValidationModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreviewModal();
        closeSendModal();
        closeValidationModal();
    }
});
</script>
@endsection
