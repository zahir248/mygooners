@extends('layouts.app')

@section('title', 'Tulis Ulasan - ' . $service->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('services.show', $service->slug) }}" class="text-arsenal hover:text-red-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tulis Ulasan</h1>
            </div>
            <p class="text-gray-600">Kongsi pengalaman anda dengan perkhidmatan ini</p>
        </div>

        <!-- Service Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center space-x-4">
                @if($service->images && is_array($service->images) && count($service->images) > 0)
                    <img src="{{ route('service.image', basename($service->images[0])) }}" 
                         alt="{{ $service->title }}" 
                         class="w-20 h-20 object-cover rounded-lg">
                @else
                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $service->title }}</h2>
                    <p class="text-gray-600">{{ $service->category }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-lg font-bold text-red-600">{{ $service->pricing }}</span>
                    </div>
                    <div class="flex items-center space-x-2 mt-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm text-gray-500">{{ $service->location }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
            <form action="{{ route('service.reviews.store', $service) }}" method="POST">
                @csrf
                
                <!-- Rating -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Rating *</label>
                    <div class="flex items-center space-x-2">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" class="hidden" required>
                            <label for="rating-{{ $i }}" class="cursor-pointer">
                                <svg class="w-12 h-12 text-gray-300 hover:text-yellow-400 transition-colors rating-star" 
                                     data-rating="{{ $i }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </label>
                        @endfor
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        <span id="rating-text">Pilih rating</span>
                    </div>
                    @error('rating')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comment -->
                <div class="mb-6">
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Ulasan Anda *</label>
                    <textarea 
                        id="comment" 
                        name="comment" 
                        rows="6" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-arsenal focus:border-transparent resize-none"
                        placeholder="Kongsi pengalaman anda dengan perkhidmatan ini. Apa yang anda suka? Apa yang boleh diperbaiki? (Minimum 10 aksara)"
                        required
                    >{{ old('comment') }}</textarea>
                    <div class="mt-1 text-sm text-gray-500">
                        <span id="char-count">0</span> / 1000 aksara
                    </div>
                    @error('comment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('services.show', $service->slug) }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-arsenal hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        Hantar Ulasan
                    </button>
                </div>
            </form>
        </div>

        <!-- Review Guidelines -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Garis Panduan Ulasan</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Berikan maklum balas yang jujur dan membina</span>
                </li>
                <li class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Fokus pada kualiti perkhidmatan, kesesuaian, dan pengalaman keseluruhan</span>
                </li>
                <li class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Elakkan serangan peribadi atau bahasa yang tidak sesuai</span>
                </li>
                <li class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Ulasan anda akan dipaparkan dengan segera kepada pelanggan lain</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingText = document.getElementById('rating-text');
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');

    // Rating star functionality
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            
            // Update radio button
            document.getElementById(`rating-${rating}`).checked = true;
            
            // Update star colors
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
            
            // Update rating text
            const ratingLabels = ['', 'Lemah', 'Sederhana', 'Baik', 'Sangat Baik', 'Cemerlang'];
            ratingText.textContent = ratingLabels[rating];
        });

        // Hover effects
        star.addEventListener('mouseenter', function() {
            const rating = this.dataset.rating;
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('text-yellow-400');
                    s.classList.remove('text-gray-300');
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            const selectedRating = document.querySelector('input[name="rating"]:checked');
            if (selectedRating) {
                const rating = selectedRating.value;
                ratingStars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('text-yellow-400');
                        s.classList.remove('text-gray-300');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            } else {
                ratingStars.forEach(s => {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                });
                ratingText.textContent = 'Pilih rating';
            }
        });
    });

    // Character count for comment
    commentTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        if (count < 10) {
            charCount.classList.add('text-red-600');
            charCount.classList.remove('text-gray-500');
        } else {
            charCount.classList.remove('text-red-600');
            charCount.classList.add('text-gray-500');
        }
    });

    // Initialize character count
    charCount.textContent = commentTextarea.value.length;
});
</script>
@endpush
