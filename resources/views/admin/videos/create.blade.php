@extends('layouts.admin')

@section('title', 'Cipta Video')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cipta Video Baharu</h1>
            <p class="mt-2 text-sm text-gray-700">Tambah dan terbitkan video Arsenal baharu</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.videos.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Video
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 px-4 sm:px-6 lg:px-8">
    @csrf
    
    <!-- Video Content -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Kandungan Video</h3>
        </div>
        <div class="px-6 py-4 space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Tajuk Video <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('title') border-red-500 @enderror"
                       placeholder="Masukkan tajuk video...">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Video <span class="text-red-500">*</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="6" 
                          required
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('description') border-red-500 @enderror"
                          placeholder="Tulis deskripsi video anda di sini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- YouTube Video ID -->
            <div>
                <label for="youtube_video_id" class="block text-sm font-medium text-gray-700 mb-2">
                    ID Video YouTube <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="youtube_video_id" 
                       id="youtube_video_id" 
                       value="{{ old('youtube_video_id') }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('youtube_video_id') border-red-500 @enderror"
                       placeholder="Contoh: dQw4w9WgXcQ">
                <p class="mt-1 text-sm text-gray-500">
                    Masukkan ID video YouTube atau URL penuh. Contoh: <strong>dQw4w9WgXcQ</strong> atau <strong>https://www.youtube.com/watch?v=dQw4w9WgXcQ</strong>
                </p>
                @error('youtube_video_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Duration -->
            <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                    Tempoh Video
                </label>
                <input type="text" 
                       name="duration" 
                       id="duration" 
                       value="{{ old('duration') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('duration') border-red-500 @enderror"
                       placeholder="Contoh: 5:30 atau 10:45">
                <p class="mt-1 text-sm text-gray-500">Format: MM:SS atau HH:MM:SS</p>
                @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thumbnail Upload -->
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                    Thumbnail Video
                </label>
                <div id="image-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                    <div id="upload-content" class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="thumbnail" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                <span>Muat naik fail</span>
                                <input id="thumbnail" name="thumbnail" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1">atau seret dan lepaskan</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF sehingga 2MB</p>
                    </div>
                </div>
                <div id="image-preview" class="mt-4 hidden">
                    <p class="text-sm font-medium text-gray-700 mb-2">Pratonton Thumbnail:</p>
                    <img id="preview-img" src="" alt="Pratonton Thumbnail" class="h-32 w-48 object-cover rounded-lg border border-gray-300">
                    <button type="button" id="remove-image" class="mt-2 text-sm text-red-600 hover:text-red-500">Buang Thumbnail</button>
                </div>
                <p class="mt-1 text-sm text-gray-500">Pilihan: Jika tidak dimuat naik, thumbnail YouTube akan digunakan secara automatik</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Live Video Preview -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pratonton Video Langsung
                </label>
                <div id="video-preview-container" class="aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300">
                    <div id="video-placeholder" class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500">Masukkan ID video YouTube untuk melihat pratonton</p>
                        </div>
                    </div>
                    <iframe id="video-iframe" 
                            class="w-full h-full hidden"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    <span id="youtube-link" class="hidden">
                        <a href="#" target="_blank" class="text-red-600 hover:text-red-800">
                            Lihat di YouTube →
                        </a>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Video Settings -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tetapan Video</h3>
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

            <!-- Tags -->
            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Tag
                </label>
                <input type="text" 
                       name="tags" 
                       id="tags" 
                       value="{{ old('tags') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('tags') border-red-500 @enderror"
                       placeholder="arsenal, premier league, match highlights, pemain">
                <p class="mt-1 text-sm text-gray-500">Pilihan: Masukkan tag yang berkaitan dipisahkan dengan koma</p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Featured Video -->
            <div class="flex items-center">
                <input type="checkbox" 
                       name="is_featured" 
                       id="is_featured" 
                       value="1"
                       {{ old('is_featured') ? 'checked' : '' }}
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                    Tandakan sebagai video pilihan
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
                       placeholder="Tajuk SEO tersuai (lalai kepada tajuk video)">
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
                          placeholder="Deskripsi SEO tersuai (lalai kepada deskripsi video)">{{ old('meta_description') }}</textarea>
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
                       placeholder="arsenal, video, perlawanan, pemain, highlights">
                <p class="mt-1 text-sm text-gray-500">Pilihan: Masukkan kata kunci yang berkaitan dipisahkan dengan koma</p>
                @error('keywords')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('admin.videos.index') }}" 
           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Batal
        </a>
        <button type="submit" 
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Cipta Video
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Image upload preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('thumbnail');
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

// Auto-update meta title when video title changes
document.getElementById('title').addEventListener('input', function() {
    const metaTitleField = document.getElementById('meta_title');
    if (!metaTitleField.value) {
        metaTitleField.value = this.value;
    }
});

// Auto-update meta description when description changes
document.getElementById('description').addEventListener('input', function() {
    const metaDescField = document.getElementById('meta_description');
    if (!metaDescField.value) {
        metaDescField.value = this.value.substring(0, 160);
    }
});

// Live video preview functionality
const youtubeVideoIdInput = document.getElementById('youtube_video_id');
const videoPlaceholder = document.getElementById('video-placeholder');
const videoIframe = document.getElementById('video-iframe');
const youtubeLink = document.getElementById('youtube-link');

function updateVideoPreview() {
    const videoId = youtubeVideoIdInput.value.trim();
    
    // Extract YouTube ID from full URL if provided
    let extractedId = videoId;
    if (videoId.includes('youtube.com') || videoId.includes('youtu.be')) {
        const match = videoId.match(/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        if (match) {
            extractedId = match[1];
        }
    }
    
    // Validate YouTube ID format (11 characters, alphanumeric, dash, underscore)
    const isValidId = /^[a-zA-Z0-9_-]{11}$/.test(extractedId);
    
    if (isValidId) {
        // Show video iframe
        videoIframe.src = `https://www.youtube.com/embed/${extractedId}`;
        videoPlaceholder.classList.add('hidden');
        videoIframe.classList.remove('hidden');
        
        // Update YouTube link
        const linkElement = youtubeLink.querySelector('a');
        linkElement.href = `https://www.youtube.com/watch?v=${extractedId}`;
        youtubeLink.classList.remove('hidden');
    } else {
        // Show placeholder
        videoPlaceholder.classList.remove('hidden');
        videoIframe.classList.add('hidden');
        youtubeLink.classList.add('hidden');
    }
}

// Update preview when YouTube ID changes
youtubeVideoIdInput.addEventListener('input', updateVideoPreview);

// Initial preview if there's an old value
if (youtubeVideoIdInput.value) {
    updateVideoPreview();
}
</script>
@endpush 