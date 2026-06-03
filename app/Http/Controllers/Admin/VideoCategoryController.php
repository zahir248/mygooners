<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VideoCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = VideoCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (VideoCategory $category) {
                $category->videos_count = $category->videosCount();

                return $category;
            });

        return view('admin.video-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.video-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:video_categories,name',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        VideoCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.video-categories.index')
            ->with('success', 'Kategori video berjaya ditambah.');
    }

    public function edit(VideoCategory $videoCategory)
    {
        return view('admin.video-categories.edit', compact('videoCategory'));
    }

    public function update(Request $request, VideoCategory $videoCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('video_categories', 'name')->ignore($videoCategory->id),
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $oldName = $videoCategory->name;

        $videoCategory->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($oldName !== $videoCategory->name) {
            Video::where('category', $oldName)->update(['category' => $videoCategory->name]);
        }

        return redirect()
            ->route('admin.video-categories.index')
            ->with('success', 'Kategori video berjaya dikemas kini.');
    }

    public function destroy(VideoCategory $videoCategory)
    {
        if ($videoCategory->videosCount() > 0) {
            return redirect()
                ->route('admin.video-categories.index')
                ->with('error', 'Kategori ini masih digunakan oleh video. Sila tukar kategori video tersebut terlebih dahulu.');
        }

        $videoCategory->delete();

        return redirect()
            ->route('admin.video-categories.index')
            ->with('success', 'Kategori video berjaya dipadam.');
    }
}
