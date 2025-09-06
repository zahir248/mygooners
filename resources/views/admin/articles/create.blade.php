@extends('layouts.admin')

@section('title', 'Cipta Artikel')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cipta Artikel Baharu</h1>
            <p class="mt-2 text-sm text-gray-700">Tulis dan terbitkan artikel Arsenal baharu</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.articles.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Artikel
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 px-4 sm:px-6 lg:px-8">
    @csrf
    
    <!-- Article Content -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Kandungan Artikel</h3>
        </div>
        <div class="px-6 py-4 space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Tajuk Artikel <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                    Ringkasan Artikel <span class="text-red-500">*</span>
                </label>
                <textarea name="excerpt" 
                          id="excerpt" 
                          rows="3" 
                          required
                          maxlength="500"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('excerpt') border-red-500 @enderror"
                          placeholder="Ringkasan ringkas artikel (maksimum 500 aksara)">{{ old('excerpt') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Maksimum 500 aksara</p>
                @error('excerpt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Kandungan Artikel <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="15" 
                          required
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('content') border-red-500 @enderror"
                          placeholder="Tulis kandungan artikel anda di sini...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cover Image Upload -->
            <div>
                <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                    Imej Muka Hadapan
                </label>
                <div id="image-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                    <div id="upload-content" class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                <span>Muat naik fail</span>
                                <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1">atau seret dan lepaskan</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF sehingga 10MB</p>
                    </div>
                </div>
                <div id="image-preview" class="mt-4 hidden">
                    <p class="text-sm font-medium text-gray-700 mb-2">Pratonton Imej:</p>
                    <img id="preview-img" src="" alt="Pratonton Imej" class="h-32 w-48 object-cover rounded-lg border border-gray-300">
                    <button type="button" id="remove-image" class="mt-2 text-sm text-red-600 hover:text-red-500">Buang Imej</button>
                </div>
                <p class="mt-1 text-sm text-gray-500">Pilihan: Tambah imej muka hadapan untuk artikel</p>
                @error('cover_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- YouTube Video URL -->
            <div>
                <label for="youtube_video_id" class="block text-sm font-medium text-gray-700 mb-2">
                    URL Video YouTube
                </label>
                <input type="text" 
                       name="youtube_video_id" 
                       id="youtube_video_id" 
                       value="{{ old('youtube_video_id') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('youtube_video_id') border-red-500 @enderror"
                       placeholder="https://youtu.be/MG5enaRS-vM">
                <p class="mt-1 text-sm text-gray-500">Pilihan: Masukkan URL penuh video YouTube (youtu.be, youtube.com, dll.)</p>
                @error('youtube_video_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Article Settings -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tetapan Artikel</h3>
        </div>
        <div class="px-6 py-4 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            id="category" 
                            required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('category') border-red-500 @enderror">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status Penerbitan <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Terbitkan Sekarang</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Featured Article -->
            <div class="flex items-center">
                <input type="checkbox" 
                       name="is_featured" 
                       id="is_featured" 
                       value="1"
                       {{ old('is_featured') ? 'checked' : '' }}
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                    Tandakan sebagai artikel pilihan
                </label>
            </div>
        </div>
    </div>

    <!-- SEO Settings -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tetapan SEO</h3>
            <p class="text-sm text-gray-500">Pilihan: Kustomkan maklumat meta SEO</p>
        </div>
        <div class="px-6 py-4 space-y-6">
            <!-- Meta Title -->
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                    Tajuk Meta
                </label>
                <input type="text" 
                       name="meta_title" 
                       id="meta_title" 
                       value="{{ old('meta_title') }}"
                       maxlength="60"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_title') border-red-500 @enderror"
                       placeholder="Tajuk SEO tersuai (lalai kepada tajuk artikel)">
                <p class="mt-1 text-sm text-gray-500">Disyorkan: 50-60 aksara</p>
                @error('meta_title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Meta Description -->
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Meta
                </label>
                <textarea name="meta_description" 
                          id="meta_description" 
                          rows="3" 
                          maxlength="160"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_description') border-red-500 @enderror"
                          placeholder="Deskripsi SEO tersuai (lalai kepada ringkasan artikel)">{{ old('meta_description') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Disyorkan: 150-160 aksara</p>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keywords -->
            <div>
                <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                    Kata Kunci
                </label>
                <input type="text" 
                       name="keywords" 
                       id="keywords" 
                       value="{{ old('keywords') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('keywords') border-red-500 @enderror"
                       placeholder="arsenal, berita, pemindahan, perlawanan">
                <p class="mt-1 text-sm text-gray-500">Pilihan: Masukkan kata kunci yang berkaitan dipisahkan dengan koma</p>
                @error('keywords')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('admin.articles.index') }}" 
           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Batal
        </a>
        <button type="button" 
                id="preview-btn"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Pratonton
        </button>
        <button type="submit" 
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Cipta Artikel
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Initialize TinyMCE
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#content',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help | link',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
        branding: false,
        promotion: false,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
});

// Image upload preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('cover_image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const uploadContent = document.getElementById('upload-content');
    const removeButton = document.getElementById('remove-image');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
                uploadContent.classList.add('hidden');
            };
            reader.readAsDataURL(file);
            
            console.log('File selected:', file.name, 'Size:', file.size, 'Type:', file.type);
        }
    });

    removeButton.addEventListener('click', function() {
        fileInput.value = '';
        imagePreview.classList.add('hidden');
        uploadContent.classList.remove('hidden');
        previewImg.src = '';
    });
});

// Auto-update meta title when article title changes
document.getElementById('title').addEventListener('input', function() {
    const metaTitleField = document.getElementById('meta_title');
    if (!metaTitleField.value) {
        metaTitleField.value = this.value;
    }
});

// Auto-update meta description when excerpt changes
document.getElementById('excerpt').addEventListener('input', function() {
    const metaDescField = document.getElementById('meta_description');
    if (!metaDescField.value) {
        metaDescField.value = this.value;
    }
});

// Character counter for excerpt
document.getElementById('excerpt').addEventListener('input', function() {
    const maxLength = 500;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    // Find or create character counter
    let counter = this.parentNode.querySelector('.char-counter');
    if (!counter) {
        counter = document.createElement('p');
        counter.className = 'char-counter mt-1 text-sm text-gray-500';
        this.parentNode.appendChild(counter);
    }
    
    counter.textContent = `${currentLength}/${maxLength} characters`;
    if (remaining < 50) {
        counter.className = 'char-counter mt-1 text-sm text-red-500';
    } else {
        counter.className = 'char-counter mt-1 text-sm text-gray-500';
    }
});

// Form submission handler for different actions
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const statusField = document.getElementById('status');
    
    // Handle save as draft button
    const draftBtn = document.querySelector('button[value="draft"]');
    if (draftBtn) {
        draftBtn.addEventListener('click', function() {
            if (statusField) statusField.value = 'draft';
        });
    }
    
    // Handle publish button
    const publishBtn = document.querySelector('button[value="publish"]');
    if (publishBtn) {
        publishBtn.addEventListener('click', function() {
            if (statusField) statusField.value = 'published';
        });
    }
});

// Preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('preview-btn');
    if (previewBtn) {
        previewBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Create a new form specifically for preview
            const originalForm = document.querySelector('form');
            const previewForm = document.createElement('form');
            previewForm.method = 'POST';
            previewForm.action = '{{ route("admin.articles.preview") }}';
            previewForm.target = '_blank';
            previewForm.enctype = 'multipart/form-data';
            previewForm.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            previewForm.appendChild(csrfToken);
            
            // Get all form inputs and copy them
            let inputs = originalForm ? originalForm.querySelectorAll('input, textarea, select') : [];
            
            // If no inputs found in form, use all inputs in document (excluding the preview button)
            if (inputs.length <= 1) {
                const allInputs = document.querySelectorAll('input, textarea, select');
                inputs = Array.from(allInputs).filter(input => 
                    input.id !== 'preview-btn' && 
                    input.type !== 'button' && 
                    input.type !== 'submit' &&
                    input.name && 
                    input.name !== ''
                );
            }
            
            inputs.forEach(function(input) {
                if (input.type === 'file') {
                    // Handle file inputs
                    if (input.files && input.files.length > 0) {
                        const fileInput = document.createElement('input');
                        fileInput.type = 'file';
                        fileInput.name = input.name;
                        fileInput.files = input.files;
                        previewForm.appendChild(fileInput);
                    }
                } else if (input.type === 'checkbox') {
                    // Handle checkboxes
                    if (input.checked) {
                        const checkbox = document.createElement('input');
                        checkbox.type = 'hidden';
                        checkbox.name = input.name;
                        checkbox.value = input.value;
                        previewForm.appendChild(checkbox);
                    }
                } else if (input.type === 'radio') {
                    // Handle radio buttons
                    if (input.checked) {
                        const radio = document.createElement('input');
                        radio.type = 'hidden';
                        radio.name = input.name;
                        radio.value = input.value;
                        previewForm.appendChild(radio);
                    }
                } else if (input.tagName === 'TEXTAREA') {
                    // Handle textareas
                    const textarea = document.createElement('input');
                    textarea.type = 'hidden';
                    textarea.name = input.name;
                    textarea.value = input.value;
                    previewForm.appendChild(textarea);
                } else if (input.tagName === 'SELECT') {
                    // Handle select dropdowns
                    const select = document.createElement('input');
                    select.type = 'hidden';
                    select.name = input.name;
                    select.value = input.value;
                    previewForm.appendChild(select);
                } else {
                    // Handle text inputs
                    const newInput = document.createElement('input');
                    newInput.type = 'hidden';
                    newInput.name = input.name;
                    newInput.value = input.value;
                    previewForm.appendChild(newInput);
                }
            });
            
            // Add form to document and submit
            document.body.appendChild(previewForm);
            previewForm.submit();
            document.body.removeChild(previewForm);
        });
    }
});
</script>
@endpush 