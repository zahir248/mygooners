@extends('layouts.admin')

@section('title', 'Create Article')

@section('header')
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Article</h1>
            <p class="mt-2 text-sm text-gray-700">Write and publish a new Arsenal article</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.articles.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Articles
            </a>
        </div>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.articles.store') }}" method="POST" class="space-y-8">
    @csrf
    
    <!-- Article Content -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Article Content</h3>
        </div>
        <div class="px-6 py-4 space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Article Title <span class="text-red-500">*</span>
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
                    Article Excerpt <span class="text-red-500">*</span>
                </label>
                <textarea name="excerpt" 
                          id="excerpt" 
                          rows="3" 
                          required
                          maxlength="500"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('excerpt') border-red-500 @enderror"
                          placeholder="Brief summary of the article (max 500 characters)">{{ old('excerpt') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Maximum 500 characters</p>
                @error('excerpt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Article Content <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="15" 
                          required
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('content') border-red-500 @enderror"
                          placeholder="Write your article content here...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cover Image URL -->
            <div>
                <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                    Cover Image URL
                </label>
                <input type="url" 
                       name="cover_image" 
                       id="cover_image" 
                       value="{{ old('cover_image') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('cover_image') border-red-500 @enderror"
                       placeholder="https://example.com/image.jpg">
                <p class="mt-1 text-sm text-gray-500">Optional: Add a cover image URL for the article</p>
                @error('cover_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- YouTube Video ID -->
            <div>
                <label for="youtube_video_id" class="block text-sm font-medium text-gray-700 mb-2">
                    YouTube Video ID
                </label>
                <input type="text" 
                       name="youtube_video_id" 
                       id="youtube_video_id" 
                       value="{{ old('youtube_video_id') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('youtube_video_id') border-red-500 @enderror"
                       placeholder="dQw4w9WgXcQ">
                <p class="mt-1 text-sm text-gray-500">Optional: YouTube video ID to embed in the article</p>
                @error('youtube_video_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Article Settings -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Article Settings</h3>
        </div>
        <div class="px-6 py-4 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            id="category" 
                            required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('category') border-red-500 @enderror">
                        <option value="">Select a category</option>
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
                        Publication Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Save as Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publish Now</option>
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
                    Mark as featured article
                </label>
            </div>
        </div>
    </div>

    <!-- SEO Settings -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">SEO Settings</h3>
            <p class="text-sm text-gray-500">Optional: Customize SEO meta information</p>
        </div>
        <div class="px-6 py-4 space-y-6">
            <!-- Meta Title -->
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Title
                </label>
                <input type="text" 
                       name="meta_title" 
                       id="meta_title" 
                       value="{{ old('meta_title') }}"
                       maxlength="60"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_title') border-red-500 @enderror"
                       placeholder="Custom SEO title (defaults to article title)">
                <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters</p>
                @error('meta_title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Meta Description -->
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Description
                </label>
                <textarea name="meta_description" 
                          id="meta_description" 
                          rows="3" 
                          maxlength="160"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_description') border-red-500 @enderror"
                          placeholder="Custom SEO description (defaults to article excerpt)">{{ old('meta_description') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters</p>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tags -->
            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Article Tags
                </label>
                <input type="text" 
                       name="tags" 
                       id="tags" 
                       value="{{ old('tags') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('tags') border-red-500 @enderror"
                       placeholder="Arsenal, Transfer, Premier League (comma-separated)">
                <p class="mt-1 text-sm text-gray-500">Separate tags with commas</p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.articles.index') }}" 
           class="px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Cancel
        </a>
        <button type="submit" 
                name="action" 
                value="draft"
                class="px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Save as Draft
        </button>
        <button type="submit" 
                name="action" 
                value="publish"
                class="px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Publish Article
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
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
    document.querySelector('button[value="draft"]').addEventListener('click', function() {
        statusField.value = 'draft';
    });
    
    // Handle publish button
    document.querySelector('button[value="publish"]').addEventListener('click', function() {
        statusField.value = 'published';
    });
});
</script>
@endpush 