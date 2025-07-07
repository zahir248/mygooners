@extends('layouts.admin')

@section('title', 'Edit Article')

@section('header')
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Article</h1>
            <p class="mt-2 text-sm text-gray-700">Update article: {{ $article->title }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('blog.show', $article->slug) }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Preview
            </a>
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
<form action="{{ route('admin.articles.update', $article->id) }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')
    
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
                       value="{{ old('title', $article->title) }}"
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
                          placeholder="Brief summary of the article (max 500 characters)">{{ old('excerpt', 'Arsenal has completed the signing of a world-class striker in what is being called the biggest transfer of the season.') }}</textarea>
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
                          placeholder="Write your article content here...">{{ old('content', $article->content) }}</textarea>
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
                       value="{{ old('cover_image', $article->cover_image) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('cover_image') border-red-500 @enderror"
                       placeholder="https://example.com/image.jpg">
                <p class="mt-1 text-sm text-gray-500">Optional: Add a cover image URL for the article</p>
                @error('cover_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                @if($article->cover_image)
                    <div class="mt-3">
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Cover Image:</p>
                        <img src="{{ $article->cover_image }}" alt="Cover Image Preview" class="h-32 w-48 object-cover rounded-lg border border-gray-300">
                    </div>
                @endif
            </div>

            <!-- YouTube Video ID -->
            <div>
                <label for="youtube_video_id" class="block text-sm font-medium text-gray-700 mb-2">
                    YouTube Video ID
                </label>
                <input type="text" 
                       name="youtube_video_id" 
                       id="youtube_video_id" 
                       value="{{ old('youtube_video_id', $article->youtube_video_id) }}"
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
                            <option value="{{ $category }}" {{ old('category', $article->category) === $category ? 'selected' : '' }}>
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
                        <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Save as Draft</option>
                        <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Published</option>
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
                       {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}
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
                       value="{{ old('meta_title', $article->meta_title) }}"
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
                          placeholder="Custom SEO description (defaults to article excerpt)">{{ old('meta_description', $article->meta_description) }}</textarea>
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
                       value="{{ old('tags', is_array($article->tags) ? implode(', ', $article->tags) : '') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('tags') border-red-500 @enderror"
                       placeholder="Arsenal, Transfer, Premier League (comma-separated)">
                <p class="mt-1 text-sm text-gray-500">Separate tags with commas</p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Article Statistics -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Article Statistics</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">1,543</div>
                    <div class="text-sm text-gray-500">Total Views</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $article->created_at->format('M j, Y') }}</div>
                    <div class="text-sm text-gray-500">Created</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $article->updated_at->format('M j, Y') }}</div>
                    <div class="text-sm text-gray-500">Last Updated</div>
                </div>
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
            {{ $article->status === 'published' ? 'Update Article' : 'Publish Article' }}
        </button>
    </div>
</form>

<!-- Delete Article Section -->
<div class="mt-8 bg-white shadow rounded-lg border-l-4 border-red-400">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Danger Zone</h3>
                <p class="text-sm text-gray-500">Permanently delete this article. This action cannot be undone.</p>
            </div>
            <button onclick="confirmDelete({{ $article->id }}, '{{ $article->title }}')" 
                    class="px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete Article
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Article</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete "<span id="deleteArticleTitle"></span>"? This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeDeleteModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
    const draftButton = document.querySelector('button[value="draft"]');
    if (draftButton) {
        draftButton.addEventListener('click', function() {
            statusField.value = 'draft';
        });
    }
    
    // Handle publish button
    const publishButton = document.querySelector('button[value="publish"]');
    if (publishButton) {
        publishButton.addEventListener('click', function() {
            statusField.value = 'published';
        });
    }
    
    // Trigger character counter on page load
    document.getElementById('excerpt').dispatchEvent(new Event('input'));
});

// Delete modal functions
function confirmDelete(articleId, articleTitle) {
    document.getElementById('deleteArticleTitle').textContent = articleTitle;
    document.getElementById('deleteForm').action = `/admin/articles/${articleId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush 