@extends('layouts.admin')

@section('title', 'Cipta Perkhidmatan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cipta Perkhidmatan Baharu</h1>
            <p class="mt-2 text-sm text-gray-700">Isi maklumat untuk perkhidmatan baharu</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.services.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Perkhidmatan
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 px-4 sm:px-6 lg:px-8">
    @csrf
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Maklumat Perkhidmatan</h3>
        </div>
        <div class="px-6 py-4 space-y-6">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Penyedia Perkhidmatan <span class="text-red-500">*</span></label>
                <select name="user_id" id="user_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('user_id') border-red-500 @enderror">
                    <option value="">Pilih pengguna</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tajuk Perkhidmatan <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="6" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pricing" class="block text-sm font-medium text-gray-700 mb-2">Harga <span class="text-red-500">*</span></label>
                    <input type="text" name="pricing" id="pricing" value="{{ old('pricing') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('pricing') border-red-500 @enderror">
                    @error('pricing')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">Maklumat Kontak <span class="text-red-500">*</span></label>
                <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('contact_info') border-red-500 @enderror">
                @error('contact_info')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                <select name="category" id="category" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('category') border-red-500 @enderror">
                    <option value="">Pilih kategori</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tag (pisahkan dengan koma)</label>
                <input type="text" name="tags" id="tags" value="{{ old('tags') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('tags') border-red-500 @enderror" placeholder="coaching, arsenal, bola">
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="trust_score" class="block text-sm font-medium text-gray-700 mb-2">Skor Kepercayaan</label>
                <input type="number" 
                       step="0.1" 
                       min="0" 
                       max="5" 
                       name="trust_score" 
                       id="trust_score" 
                       value="{{ old('trust_score', 0) }}" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('trust_score') border-red-500 @enderror" 
                       placeholder="0 - 5"
                       oninput="validateTrustScore(this)">
                <p id="trust_score_help" class="mt-1 text-sm text-gray-500">Masukkan nilai antara 0 hingga 5</p>
                @error('trust_score')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="is_verified" class="ml-2 block text-sm text-gray-900">Tandakan sebagai perkhidmatan disahkan</label>
            </div>
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Gambar Perkhidmatan</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                <p class="mt-1 text-sm text-gray-500">Boleh muat naik lebih dari satu gambar. PNG, JPG, GIF sehingga 10MB setiap satu.</p>
                @error('images')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm font-medium">Cipta Perkhidmatan</button>
    </div>
</form>

@push('scripts')
<script>
function validateTrustScore(input) {
    const value = parseFloat(input.value);
    const helpText = document.getElementById('trust_score_help');
    
    if (isNaN(value)) {
        helpText.textContent = 'Sila masukkan nombor yang sah';
        helpText.className = 'mt-1 text-sm text-red-600';
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
    } else if (value < 0) {
        helpText.textContent = 'Nilai minimum ialah 0';
        helpText.className = 'mt-1 text-sm text-red-600';
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
        input.value = 0;
    } else if (value > 5) {
        helpText.textContent = 'Nilai maksimum ialah 5';
        helpText.className = 'mt-1 text-sm text-red-600';
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
        input.value = 5;
    } else {
        helpText.textContent = 'Masukkan nilai antara 0 hingga 5';
        helpText.className = 'mt-1 text-sm text-gray-500';
        input.classList.remove('border-red-500');
        input.classList.add('border-green-500');
    }
}
</script>
@endpush

@endsection 