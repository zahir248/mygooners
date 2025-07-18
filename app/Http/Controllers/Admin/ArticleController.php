<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Category filter
        if ($request->filled('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }
        
        // Featured filter
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }
        
        $articles = $query->orderBy('created_at', 'desc')->get();
        
        // Get unique categories for filter dropdown
        $categories = Article::distinct()->pluck('category')->sort()->values();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = [
            'Transfer News',
            'Match Reports',
            'Training',
            'Women\'s Team',
            'Transfer Rumours',
            'Analysis',
            'History'
        ];

        return view('admin.articles.create', compact('categories'));
    }

    private function extractYoutubeId($url)
    {
        // Handles youtu.be, youtube.com/watch?v=, youtube.com/embed/, etc.
        preg_match(
            '/(?:youtube\\.com\\/(?:[^\\/\\n\\s]+\\/\\S+\\/|(?:v|e(?:mbed)?|shorts)\\/|.*[?&]v=)|youtu\\.be\\/)([\\w-]{11})/i',
            $url,
            $matches
        );
        return $matches[1] ?? null;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'youtube_video_id' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_featured'] = $request->has('is_featured');
        
        // Handle cover image upload with debugging
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            
            // Debug information
            \Log::info('Image upload detected', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'extension' => $image->getClientOriginalExtension(),
                'is_valid' => $image->isValid(),
                'error' => $image->getError()
            ]);
            
            if (!$image->isValid()) {
                \Log::error('Image upload failed', [
                    'error' => $image->getError(),
                    'error_message' => $image->getErrorMessage()
                ]);
                return back()->withErrors(['cover_image' => 'Image upload failed: ' . $image->getErrorMessage()]);
            }
            
            $filename = 'articles/' . time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            
            // Store the file
            try {
                // Use putFileAs instead of storeAs for better control
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename), 
                    $image, 
                    basename($filename)
                );
                
                \Log::info('Image stored', [
                    'filename' => $filename,
                    'stored_path' => $stored,
                    'exists' => Storage::disk('public')->exists($filename),
                    'full_path' => storage_path('app/public/' . $filename)
                ]);
                
                if ($stored) {
                    $data['cover_image'] = $filename;
                    
                    // Verify the file was actually created
                    if (!Storage::disk('public')->exists($filename)) {
                        \Log::error('File was not created despite successful storage', [
                            'filename' => $filename,
                            'stored_path' => $stored
                        ]);
                        return back()->withErrors(['cover_image' => 'File was not created properly']);
                    }
                } else {
                    \Log::error('Failed to store image file');
                    return back()->withErrors(['cover_image' => 'Failed to store image file']);
                }
            } catch (Exception $e) {
                \Log::error('Exception during image storage', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->withErrors(['cover_image' => 'Error storing image: ' . $e->getMessage()]);
            }
        } else {
            \Log::info('No image file detected in request', [
                'files' => $request->allFiles(),
                'has_file' => $request->hasFile('cover_image')
            ]);
        }
        
        // Extract YouTube video ID
        if ($request->youtube_video_id) {
            $data['youtube_video_id'] = $this->extractYoutubeId($request->youtube_video_id) ?? $request->youtube_video_id;
        }
        
        // Set published_at if status is published
        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        // Convert keywords to tags array if provided
        if ($request->keywords) {
            $data['tags'] = array_map('trim', explode(',', $request->keywords));
        }

        $article = Article::create($data);
        
        \Log::info('Article created', [
            'id' => $article->id,
            'title' => $article->title,
            'cover_image' => $article->cover_image
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully!');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);

        $categories = [
            'Transfer News',
            'Match Reports',
            'Training',
            'Women\'s Team',
            'Transfer Rumours',
            'Analysis',
            'History'
        ];

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'youtube_video_id' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable|string'
        ]);

        $article = Article::findOrFail($id);
        
        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_featured'] = $request->has('is_featured');
        
        // Handle cover image upload with debugging
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($article->cover_image && Storage::disk('public')->exists($article->cover_image)) {
                Storage::disk('public')->delete($article->cover_image);
            }
            
            $image = $request->file('cover_image');
            
            // Debug information
            \Log::info('Image upload detected in update', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'extension' => $image->getClientOriginalExtension()
            ]);
            
            $filename = 'articles/' . time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            
            // Store the file
            try {
                // Use putFileAs instead of storeAs for better control
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename), 
                    $image, 
                    basename($filename)
                );
                
                \Log::info('Image stored in update', [
                    'filename' => $filename,
                    'stored_path' => $stored,
                    'exists' => Storage::disk('public')->exists($filename),
                    'full_path' => storage_path('app/public/' . $filename)
                ]);
                
                if ($stored) {
                    $data['cover_image'] = $filename;
                    
                    // Verify the file was actually created
                    if (!Storage::disk('public')->exists($filename)) {
                        \Log::error('File was not created despite successful storage in update', [
                            'filename' => $filename,
                            'stored_path' => $stored
                        ]);
                        return back()->withErrors(['cover_image' => 'File was not created properly']);
                    }
                } else {
                    \Log::error('Failed to store image file in update');
                    return back()->withErrors(['cover_image' => 'Failed to store image file']);
                }
            } catch (Exception $e) {
                \Log::error('Exception during image storage in update', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->withErrors(['cover_image' => 'Error storing image: ' . $e->getMessage()]);
            }
        } else {
            \Log::info('No image file detected in update request');
        }
        
        // Extract YouTube video ID
        if ($request->youtube_video_id) {
            $data['youtube_video_id'] = $this->extractYoutubeId($request->youtube_video_id) ?? $request->youtube_video_id;
        }
        
        // Set published_at if status is published and wasn't published before
        if ($request->status === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        }

        // Convert keywords to tags array if provided
        if ($request->keywords) {
            $data['tags'] = array_map('trim', explode(',', $request->keywords));
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully!');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        
        // Delete cover image if exists
        if ($article->cover_image && Storage::disk('public')->exists($article->cover_image)) {
            Storage::disk('public')->delete($article->cover_image);
        }
        
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully!');
    }
} 