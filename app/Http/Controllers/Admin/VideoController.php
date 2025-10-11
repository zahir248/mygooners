<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class VideoController extends Controller
{
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

    public function index(Request $request)
    {
        $query = Video::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('youtube_video_id', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->where('is_featured', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
        }

        $videos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get unique categories for filter
        $categories = Video::distinct()->pluck('category')->filter()->values();

        return view('admin.videos.index', compact('videos', 'categories'));
    }

    public function create()
    {
        // Existing video categories
        $videoCategories = [
            'Match Highlights' => 'Match Highlights',
            'Player Interviews' => 'Player Interviews',
            'Manager Press Conferences' => 'Manager Press Conferences',
            'Behind the Scenes' => 'Behind the Scenes',
            'Training Sessions' => 'Training Sessions',
            'Fan Content' => 'Fan Content',
            'Analysis' => 'Analysis',
            'News' => 'News'
        ];

        // Article categories
        $articleCategories = [
            'Perkembangan Kelab' => 'Perkembangan Kelab',
            'EPL' => 'EPL',
            'UCL' => 'UCL',
            'Bolasepak' => 'Bolasepak',
            'Piala Dunia' => 'Piala Dunia',
            'Euro' => 'Euro',
            'Berita Perpindahan' => 'Berita Perpindahan',
            'Analisis' => 'Analisis',
            'Bundesliga' => 'Bundesliga',
            'Serie A' => 'Serie A',
            'Ligue 1' => 'Ligue 1',
            'Antarabangsa' => 'Antarabangsa',
            'La Liga' => 'La Liga',
            'Lain-lain' => 'Lain-lain'
        ];

        // Combine video and article categories
        $categories = array_merge($videoCategories, $articleCategories);

        return view('admin.videos.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'youtube_video_id' => 'required|string|max:255', // Allow full URLs
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|string|max:10'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['tags'] = $request->tags ? explode(',', $request->tags) : [];
        $data['is_featured'] = $request->has('is_featured');
        $data['published_at'] = $request->status === 'published' ? now() : null;

        // Extract YouTube video ID
        if ($request->youtube_video_id) {
            $data['youtube_video_id'] = $this->extractYoutubeId($request->youtube_video_id) ?? $request->youtube_video_id;
        }

        // Handle thumbnail upload with debugging
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            
            // Debug information
            \Log::info('Thumbnail upload detected', [
                'original_name' => $thumbnail->getClientOriginalName(),
                'size' => $thumbnail->getSize(),
                'mime_type' => $thumbnail->getMimeType(),
                'extension' => $thumbnail->getClientOriginalExtension(),
                'is_valid' => $thumbnail->isValid(),
                'error' => $thumbnail->getError()
            ]);
            
            if (!$thumbnail->isValid()) {
                \Log::error('Thumbnail upload failed', [
                    'error' => $thumbnail->getError(),
                    'error_message' => $thumbnail->getErrorMessage()
                ]);
                return back()->withErrors(['thumbnail' => 'Thumbnail upload failed: ' . $thumbnail->getErrorMessage()]);
            }
            
            $filename = 'videos/' . time() . '_' . Str::slug($request->title) . '.' . $thumbnail->getClientOriginalExtension();
            
            // Store the file
            try {
                // Use putFileAs instead of storeAs for better control
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename), 
                    $thumbnail, 
                    basename($filename)
                );
                
                \Log::info('Thumbnail stored', [
                    'filename' => $filename,
                    'stored_path' => $stored,
                    'exists' => Storage::disk('public')->exists($filename),
                    'full_path' => storage_path('app/public/' . $filename)
                ]);
                
                if ($stored) {
                    $data['thumbnail'] = $filename;
                    
                    // Verify the file was actually created
                    if (!Storage::disk('public')->exists($filename)) {
                        \Log::error('File was not created despite successful storage', [
                            'filename' => $filename,
                            'stored_path' => $stored
                        ]);
                        return back()->withErrors(['thumbnail' => 'File was not created properly']);
                    }
                } else {
                    \Log::error('Failed to store thumbnail file');
                    return back()->withErrors(['thumbnail' => 'Failed to store thumbnail file']);
                }
            } catch (Exception $e) {
                \Log::error('Exception during thumbnail storage', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->withErrors(['thumbnail' => 'Error storing thumbnail: ' . $e->getMessage()]);
            }
        } else {
            \Log::info('No thumbnail file detected in request', [
                'files' => $request->allFiles(),
                'has_file' => $request->hasFile('thumbnail')
            ]);
        }

        try {
            Video::create($data);
            return redirect()->route('admin.videos.index')
                           ->with('success', 'Video berjaya dicipta!');
        } catch (\Exception $e) {
            Log::error('Error creating video: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ralat semasa mencipta video. Sila cuba lagi.');
        }
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        
        // Existing video categories
        $videoCategories = [
            'Match Highlights' => 'Match Highlights',
            'Player Interviews' => 'Player Interviews',
            'Manager Press Conferences' => 'Manager Press Conferences',
            'Behind the Scenes' => 'Behind the Scenes',
            'Training Sessions' => 'Training Sessions',
            'Fan Content' => 'Fan Content',
            'Analysis' => 'Analysis',
            'News' => 'News'
        ];

        // Article categories
        $articleCategories = [
            'Perkembangan Kelab' => 'Perkembangan Kelab',
            'EPL' => 'EPL',
            'UCL' => 'UCL',
            'Bolasepak' => 'Bolasepak',
            'Piala Dunia' => 'Piala Dunia',
            'Euro' => 'Euro',
            'Berita Perpindahan' => 'Berita Perpindahan',
            'Analisis' => 'Analisis',
            'Bundesliga' => 'Bundesliga',
            'Serie A' => 'Serie A',
            'Ligue 1' => 'Ligue 1',
            'Antarabangsa' => 'Antarabangsa',
            'La Liga' => 'La Liga',
            'Lain-lain' => 'Lain-lain'
        ];

        // Combine video and article categories
        $categories = array_merge($videoCategories, $articleCategories);

        return view('admin.videos.edit', compact('video', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'youtube_video_id' => 'required|string|max:255', // Allow full URLs
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|string|max:10'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['tags'] = $request->tags ? explode(',', $request->tags) : [];
        $data['is_featured'] = $request->has('is_featured');
        
        // Extract YouTube video ID
        if ($request->youtube_video_id) {
            $data['youtube_video_id'] = $this->extractYoutubeId($request->youtube_video_id) ?? $request->youtube_video_id;
        }
        
        // Set published_at if status is changing to published
        if ($request->status === 'published' && $video->status !== 'published') {
            $data['published_at'] = now();
        }

        // Handle thumbnail removal
        if ($request->has('remove_thumbnail') && $request->remove_thumbnail == '1') {
            // Delete old thumbnail if exists
            if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
                Storage::disk('public')->delete($video->thumbnail);
                \Log::info('Thumbnail removed', ['filename' => $video->thumbnail]);
            }
            $data['thumbnail'] = null; // Set thumbnail to null to use YouTube thumbnail
        }
        // Handle thumbnail upload with debugging
        elseif ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            
            $thumbnail = $request->file('thumbnail');
            
            // Debug information
            \Log::info('Thumbnail upload detected in update', [
                'original_name' => $thumbnail->getClientOriginalName(),
                'size' => $thumbnail->getSize(),
                'mime_type' => $thumbnail->getMimeType(),
                'extension' => $thumbnail->getClientOriginalExtension()
            ]);
            
            $filename = 'videos/' . time() . '_' . Str::slug($request->title) . '.' . $thumbnail->getClientOriginalExtension();
            
            // Store the file
            try {
                // Use putFileAs instead of storeAs for better control
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename), 
                    $thumbnail, 
                    basename($filename)
                );
                
                \Log::info('Thumbnail stored in update', [
                    'filename' => $filename,
                    'stored_path' => $stored,
                    'exists' => Storage::disk('public')->exists($filename),
                    'full_path' => storage_path('app/public/' . $filename)
                ]);
                
                if ($stored) {
                    $data['thumbnail'] = $filename;
                    
                    // Verify the file was actually created
                    if (!Storage::disk('public')->exists($filename)) {
                        \Log::error('File was not created despite successful storage in update', [
                            'filename' => $filename,
                            'stored_path' => $stored
                        ]);
                        return back()->withErrors(['thumbnail' => 'File was not created properly']);
                    }
                } else {
                    \Log::error('Failed to store thumbnail file in update');
                    return back()->withErrors(['thumbnail' => 'Failed to store thumbnail file']);
                }
            } catch (Exception $e) {
                \Log::error('Exception during thumbnail storage in update', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->withErrors(['thumbnail' => 'Error storing thumbnail: ' . $e->getMessage()]);
            }
        }

        try {
            $video->update($data);
            
            // Custom success message based on what was updated
            $successMessage = 'Video berjaya dikemas kini!';
            if ($request->has('remove_thumbnail') && $request->remove_thumbnail == '1') {
                $successMessage = 'Video berjaya dikemas kini! Thumbnail telah dibuang dan thumbnail YouTube akan digunakan.';
            }
            
            return redirect()->route('admin.videos.index')
                           ->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error updating video: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ralat semasa mengemas kini video. Sila cuba lagi.');
        }
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        try {
            // Delete thumbnail if exists
            if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
                Storage::disk('public')->delete($video->thumbnail);
            }

            $video->delete();
            return redirect()->route('admin.videos.index')
                           ->with('success', 'Video berjaya dipadam!');
        } catch (\Exception $e) {
            Log::error('Error deleting video: ' . $e->getMessage());
            return back()->with('error', 'Ralat semasa memadam video. Sila cuba lagi.');
        }
    }
} 